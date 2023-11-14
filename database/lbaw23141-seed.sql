--
-- Use a specific schema and set it as default - lbaw23141
--

DROP SCHEMA IF EXISTS lbaw23141 CASCADE;
CREATE SCHEMA IF NOT EXISTS lbaw23141;
SET search_path TO lbaw23141;

--
--DROP the old schema
--
DROP TABLE IF EXISTS notification CASCADE;
DROP TABLE IF EXISTS message CASCADE;
DROP TABLE IF EXISTS group_member CASCADE;
DROP TABLE IF EXISTS group_chat CASCADE;
DROP TABLE IF EXISTS report CASCADE;
DROP TABLE IF EXISTS bookmarks CASCADE;
DROP TABLE IF EXISTS mention CASCADE;
DROP TABLE IF EXISTS comment_likes CASCADE;
DROP TABLE IF EXISTS post_likes CASCADE;
DROP TABLE IF EXISTS follow_request CASCADE;
DROP TABLE IF EXISTS comment CASCADE;
DROP TABLE IF EXISTS post CASCADE;
DROP TABLE IF EXISTS users CASCADE;

DROP TYPE IF EXISTS user_types;
DROP TYPE IF EXISTS notification_types;
DROP TYPE IF EXISTS report_types;
DROP TYPE IF EXISTS request_status;

DROP FUNCTION IF EXISTS user_search_update CASCADE;
DROP FUNCTION IF EXISTS post_search_update CASCADE;
DROP FUNCTION IF EXISTS comment_search_update CASCADE;
DROP FUNCTION IF EXISTS group_chat_search_update CASCADE;
DROP FUNCTION IF EXISTS notify_follow_request CASCADE;
DROP FUNCTION IF EXISTS reject_inappropriate_posts CASCADE;

-- Create ENUM types
CREATE TYPE user_types AS ENUM ('normal_user', 'admin', 'suspended');
CREATE TYPE notification_types AS ENUM ('liked_comment', 'reply_comment', 'request_follow', 'started_following', 'accepted_follow', 'joined_group', 'group_invite', 'liked_post', 'comment_post');
CREATE TYPE report_types AS ENUM ('harassment', 'hate_speech', 'inappropriate_content', 'spam', 'self_harm');
CREATE TYPE request_status AS ENUM ('accepted', 'rejected', 'waiting');

-- Table: users (R01)
CREATE TABLE users (
    user_id SERIAL PRIMARY KEY,
    username VARCHAR(255) UNIQUE NOT NULL,
    email VARCHAR(255) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    name VARCHAR(255) NOT NULL,
    bio TEXT,
    private BOOLEAN NOT NULL DEFAULT false,
    user_type user_types NOT NULL
);

-- Table: post (R02)
CREATE TABLE post (
    post_id SERIAL PRIMARY KEY,
    owner_id INTEGER REFERENCES users(user_id) NOT NULL,
    image TEXT,
    content TEXT,
    date DATE NOT NULL CHECK (date <= CURRENT_DATE)
);

-- Table: comment (R03)
CREATE TABLE comment (
    comment_id SERIAL PRIMARY KEY,
    author_id INTEGER REFERENCES users(user_id) NOT NULL,
    post_id INTEGER REFERENCES post(post_id) NOT NULL,
    content TEXT,
    date DATE NOT NULL CHECK (date <= CURRENT_DATE),
    previous INTEGER REFERENCES comment(comment_id) DEFAULT NULL
);

-- Table: group_chat (R07)
CREATE TABLE group_chat (
    group_id SERIAL PRIMARY KEY,
    owner_id INTEGER REFERENCES users(user_id) NOT NULL,
    name VARCHAR(255) NOT NULL,
    description TEXT
);

-- Table: message (R05)
CREATE TABLE message (
    message_id SERIAL PRIMARY KEY,
    emitter_id INTEGER REFERENCES users(user_id) NOT NULL,
    group_id INTEGER REFERENCES group_chat(group_id) NOT NULL,
    content TEXT NOT NULL,
    date DATE NOT NULL CHECK (date <= CURRENT_DATE),
    viewed BOOLEAN NOT NULL DEFAULT false
);

-- Table: follow_request (R06)
CREATE TABLE follow_request (
    req_id INTEGER REFERENCES users(user_id) NOT NULL,
    rcv_id INTEGER REFERENCES users(user_id) NOT NULL,
    date DATE NOT NULL CHECK (date <= CURRENT_DATE),
    status request_status NOT NULL,
	PRIMARY KEY (req_id, rcv_id)
);



-- Table: group_member (R08)
CREATE TABLE group_member (
    user_id INTEGER REFERENCES users(user_id),
    group_id INTEGER REFERENCES group_chat(group_id),
    status VARCHAR(50) NOT NULL CHECK (status IN ('request_status')),
	PRIMARY KEY (user_id, group_id)
);

-- Table: post_likes (R09)
CREATE TABLE post_likes (
    user_id INTEGER REFERENCES users(user_id),
    post_id INTEGER REFERENCES post(post_id),
    PRIMARY KEY (user_id, post_id)
);

-- Table: comment_likes (R10)
CREATE TABLE comment_likes (
    user_id INTEGER REFERENCES users(user_id),
    comment_id INTEGER REFERENCES comment(comment_id),
    PRIMARY KEY (user_id, comment_id)
);

-- Table: mention (R11)
CREATE TABLE mention (
    post_id INTEGER REFERENCES post(post_id),
    user_mentioned INTEGER REFERENCES users(user_id),
    PRIMARY KEY (post_id, user_mentioned)
);

-- Table: bookmarks (R12)
CREATE TABLE bookmarks (
    bookmarked_post INTEGER REFERENCES post(post_id),
    user_id INTEGER REFERENCES users(user_id),
    PRIMARY KEY (bookmarked_post, user_id)
);

-- Table: report (R13)
CREATE TABLE report (
    report_id SERIAL PRIMARY KEY,
    user_id INTEGER REFERENCES users(user_id),
    post_id INTEGER REFERENCES post(post_id),
    date DATE NOT NULL CHECK (date <= CURRENT_DATE),
    report_type report_types NOT NULL
);

-- Table: notification (R04)
CREATE TABLE notification (
    notification_id SERIAL PRIMARY KEY,
    date DATE NOT NULL CHECK (date <= CURRENT_DATE),
    notified_user INTEGER REFERENCES users(user_id) NOT NULL,
    notification_type notification_types NOT NULL CHECK (
        (notification_type IN ('liked_comment', 'reply_comment') AND post_id IS NULL AND group_id IS NULL) OR
        (notification_type IN ('request_follow', 'started_following', 'accepted_follow') AND comment_id IS NULL AND post_id IS NULL AND group_id IS NULL) OR
        (notification_type IN ('joined_group', 'group_invite') AND comment_id IS NULL AND post_id IS NULL) OR
        (notification_type IN ('liked_post', 'comment_post') AND comment_id IS NULL AND group_id IS NULL)
    ),
    comment_id INTEGER REFERENCES comment(comment_id),
    post_id INTEGER REFERENCES post(post_id),
    group_id INTEGER REFERENCES group_chat(group_id),
    viewed BOOLEAN NOT NULL DEFAULT false
);

------------------------------
-- INDEXES
------------------------------

CREATE INDEX owner_id_post ON post USING hash (owner_id);

CREATE INDEX author_id_comment ON comment USING hash (author_id);

CREATE INDEX notified_user_notification ON notification USING btree (notified_user);

CREATE INDEX user_id_bookmarks ON bookmarks USING btree (user_id);
CLUSTER bookmarks USING user_id_bookmarks;

------------------------------
-- FTS INDEXES
------------------------------

-- Index 06 --

-- Add column to user to store computed ts_vectors.
ALTER TABLE users
ADD COLUMN tsvectors TSVECTOR;

-- Create a function to automatically update ts_vectors.
CREATE FUNCTION user_search_update() RETURNS TRIGGER AS $$
BEGIN
IF TG_OP = 'INSERT' THEN
    NEW.tsvectors = (
        setweight(to_tsvector('portuguese', NEW.name), 'A') ||
        setweight(to_tsvector('portuguese', NEW.username), 'B') ||
        setweight(to_tsvector('portuguese', NEW.bio), 'C')
    );
END IF;
IF TG_OP = 'UPDATE' THEN
    IF (NEW.name <> OLD.name OR NEW.username <> OLD.username  OR NEW.bio <> OLD.bio) THEN
        NEW.tsvectors = (
            setweight(to_tsvector('portuguese', NEW.name), 'A') ||
            setweight(to_tsvector('portuguese', NEW.username), 'B') ||
            setweight(to_tsvector('portuguese', NEW.bio), 'C')
        );
    END IF;
END IF;
RETURN NEW;
END $$
LANGUAGE plpgsql;

-- Create a trigger before insert or update on users
CREATE TRIGGER user_search_update
BEFORE INSERT OR UPDATE ON users
FOR EACH ROW
EXECUTE PROCEDURE user_search_update();

-- Create a GIN index for ts_vectors.
CREATE INDEX search_user ON users USING GIN (tsvectors);

-- Index 07 --

-- Add column to post to store computed ts_vectors.
ALTER TABLE post
ADD COLUMN tsvectors TSVECTOR;

-- Create a function to automatically update ts_vectors.
CREATE FUNCTION post_search_update() RETURNS TRIGGER AS $$
BEGIN
IF TG_OP = 'INSERT' THEN
    NEW.tsvectors = to_tsvector('portuguese', NEW.content);
END IF;
IF TG_OP = 'UPDATE' THEN
    IF (NEW.content <> OLD.content) THEN
        NEW.tsvectors = to_tsvector('portuguese', NEW.content);
    END IF;
END IF;
RETURN NEW;
END $$
LANGUAGE plpgsql;

-- Create a trigger before insert or update on post
CREATE TRIGGER post_search_update
BEFORE INSERT OR UPDATE ON post
FOR EACH ROW
EXECUTE PROCEDURE post_search_update();

-- Create a GIN index for ts_vectors.
CREATE INDEX search_post ON post USING GIN (tsvectors);

-- Index 08 --

-- Add column to comment to store computed ts_vectors.
ALTER TABLE comment
ADD COLUMN tsvectors TSVECTOR;

-- Create a function to automatically update ts_vectors.
CREATE FUNCTION comment_search_update() RETURNS TRIGGER AS $$
BEGIN
IF TG_OP = 'INSERT' THEN
    NEW.tsvectors = to_tsvector('portuguese', NEW.content);
END IF;
IF TG_OP = 'UPDATE' THEN
    IF (NEW.content <> OLD.content) THEN
        NEW.tsvectors = to_tsvector('portuguese', NEW.content);
    END IF;
END IF;
RETURN NEW;
END $$
LANGUAGE plpgsql;

-- Create a trigger before insert or update on comment
CREATE TRIGGER comment_search_update
BEFORE INSERT OR UPDATE ON comment
FOR EACH ROW
EXECUTE PROCEDURE comment_search_update();

-- Create a GIN index for ts_vectors.
CREATE INDEX search_comment ON comment USING GIN (tsvectors);

-- Index 09 --

-- Add column to group_chat to store computed ts_vectors.
ALTER TABLE group_chat
ADD COLUMN tsvectors TSVECTOR;

-- Create a function to automatically update ts_vectors.
CREATE FUNCTION group_chat_search_update() RETURNS TRIGGER AS $$
BEGIN
IF TG_OP = 'INSERT' THEN
    NEW.tsvectors = (
        setweight(to_tsvector('portuguese', NEW.name), 'A') ||
        setweight(to_tsvector('portuguese', NEW.description), 'B')
    );
END IF;
IF TG_OP = 'UPDATE' THEN
    IF (NEW.name <> OLD.name OR NEW.description <> OLD.description) THEN
        NEW.tsvectors = (
            setweight(to_tsvector('portuguese', NEW.name), 'A') ||
            setweight(to_tsvector('portuguese', NEW.description), 'B')
        );
    END IF;
END IF;
RETURN NEW;
END $$
LANGUAGE plpgsql;

-- Create a trigger before insert or update on group_chat
CREATE TRIGGER group_chat_search_update
BEFORE INSERT OR UPDATE ON group_chat
FOR EACH ROW
EXECUTE PROCEDURE group_chat_search_update();

-- Create a GIN index for ts_vectors.
CREATE INDEX search_group_chat ON group_chat USING GIN (tsvectors);

------------------------------
-- TRIGGERS
------------------------------

CREATE FUNCTION notify_follow_request() RETURNS TRIGGER AS
$BODY$
BEGIN
    INSERT INTO notifications (user_id, message, created_at)
    VALUES (NEW.receiver_id, 'You have a new friend request from ' || NEW.sender_name);
    RETURN NEW;
END
$BODY$
LANGUAGE plpgsql;

CREATE TRIGGER notify_follow_request
AFTER INSERT ON follow_request
FOR EACH ROW
EXECUTE PROCEDURE notify_follow_request();

CREATE FUNCTION reject_inappropriate_posts() RETURNS TRIGGER AS
$BODY$
BEGIN
    IF NEW.content ILIKE ANY (ARRAY['%inappropriate%', '%offensive%', '%spam%']) THEN
        RETURN NULL;
    END IF;
    RETURN NEW;
END
$BODY$
LANGUAGE plpgsql;

CREATE TRIGGER reject_inappropriate_posts
BEFORE INSERT ON post
FOR EACH ROW
EXECUTE PROCEDURE reject_inappropriate_posts();

CREATE OR REPLACE FUNCTION prevent_duplicate_post_likes() RETURNS TRIGGER AS 
$$
BEGIN
    IF EXISTS (
        SELECT 1
        FROM post_likes
        WHERE user_id = NEW.user_id AND post_id = NEW.post_id
    ) THEN
    RAISE EXCEPTION 'User has already liked this post.';
    END IF;
    RETURN NEW;
END;
$$ LANGUAGE plpgsql;

CREATE TRIGGER enforce_unique_post_likes
BEFORE INSERT ON post_likes
FOR EACH ROW
EXECUTE FUNCTION prevent_duplicate_post_likes();

CREATE OR REPLACE FUNCTION enforce_message_sender_membership()
RETURNS TRIGGER AS $$
BEGIN
    IF NOT EXISTS (
        SELECT 1
        FROM group_member
        WHERE user_id = NEW.emitter_id AND group_id = NEW.group_id
    ) THEN
    RAISE EXCEPTION 'User is not a member of the group chat.';
    END IF;
    RETURN NEW;
END;
$$ LANGUAGE plpgsql;

CREATE TRIGGER check_message_sender_membership
BEFORE INSERT ON message
FOR EACH ROW
EXECUTE FUNCTION enforce_message_sender_membership();

CREATE OR REPLACE FUNCTION enforce_comment_privacy()
RETURNS TRIGGER AS $$
BEGIN
    IF NOT EXISTS (
        SELECT 1
        FROM users AS post_owner
        WHERE post_owner.user_id = NEW.author_id
        AND (post_owner.private = false OR EXISTS (
            SELECT 1
            FROM follow_request
            WHERE req_id = NEW.author_id
            AND rcv_id = post_owner.user_id
            AND status = 'accepted'
        ))
    ) THEN
    RAISE EXCEPTION 'User is not allowed to comment on this post.';
    END IF;
    RETURN NEW;
END;
$$ LANGUAGE plpgsql;

CREATE TRIGGER check_comment_privacy
BEFORE INSERT ON comment
FOR EACH ROW
EXECUTE FUNCTION enforce_comment_privacy();

CREATE OR REPLACE FUNCTION prevent_self_follow()
RETURNS TRIGGER AS $$
BEGIN
IF NEW.req_id = NEW.rcv_id THEN
        RAISE EXCEPTION 'A user cannot send a follow request to themselves.';
    END IF;
    RETURN NEW;
END;
$$ LANGUAGE plpgsql;

CREATE TRIGGER check_self_follow
BEFORE INSERT ON follow_request
FOR EACH ROW
EXECUTE FUNCTION prevent_self_follow();

CREATE OR REPLACE FUNCTION prevent_duplicate_group_membership()
RETURNS TRIGGER AS $$
BEGIN
    IF EXISTS (
        SELECT 1
        FROM group_member
        WHERE user_id = NEW.user_id AND group_id = NEW.group_id
    ) THEN
    RAISE EXCEPTION 'User is already a member of this group chat.';
    END IF;
    RETURN NEW;
END;
$$ LANGUAGE plpgsql;

CREATE TRIGGER enforce_unique_group_membership
BEFORE INSERT ON group_member
FOR EACH ROW
EXECUTE FUNCTION prevent_duplicate_group_membership();

CREATE OR REPLACE FUNCTION prevent_duplicate_follow_request()
RETURNS TRIGGER AS $$
BEGIN
    IF EXISTS (
        SELECT 1
        FROM follow_request
        WHERE req_id = NEW.req_id AND rcv_id = NEW.rcv_id
        AND (status = 'pending' OR status = 'accepted')
    ) THEN
    RAISE EXCEPTION 'User has already requested to follow or is already following.';
    END IF;
    RETURN NEW;
END;
$$ LANGUAGE plpgsql;

CREATE TRIGGER enforce_unique_follow_request
BEFORE INSERT ON follow_request
FOR EACH ROW
EXECUTE FUNCTION prevent_duplicate_follow_request();

CREATE OR REPLACE FUNCTION ensure_owner_is_member()
RETURNS TRIGGER AS $$
BEGIN
    IF NOT EXISTS (
        SELECT 1
        FROM group_member
        WHERE user_id = NEW.owner_id AND group_id = NEW.group_id
    ) THEN
    RAISE EXCEPTION 'The group chat owner must be a member of the group chat.';
    END IF;
    RETURN NEW;
END;
$$ LANGUAGE plpgsql;

CREATE TRIGGER check_owner_membership
BEFORE INSERT ON group_chat
FOR EACH ROW
EXECUTE FUNCTION ensure_owner_is_member();

------------------------------
-- TRANSACTIONS
------------------------------

CREATE OR REPLACE FUNCTION create_post_with_mentions(
    IN owner_id INTEGER,
    IN post_content TEXT
)
RETURNS VOID AS $$
DECLARE
    newpost_id INTEGER;
BEGIN
    -- Set the isolation level to Repeatable Read
    SET TRANSACTION ISOLATION LEVEL REPEATABLE READ;

    -- Create the post and obtain the post_id
    INSERT INTO post (owner_id, content, date)
    VALUES (owner_id, post_content, NOW())
    RETURNING post_id INTO newpost_id;

    -- Create mention records for the post by extracting mentions from the content
    INSERT INTO mention (post_id, user_mentioned)
    SELECT newpost_id, user_id
    FROM users
    WHERE regexp_matches(post_content, '@([A-Za-z0-9_]+)', 'g') IS NOT NULL;

    -- Commit the transaction if everything is successful
    COMMIT;

    -- Return nothing (VOID)
    RETURN;
END;
$$ LANGUAGE plpgsql;