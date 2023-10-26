create schema if not exists lbaw2255;

SET DateStyle TO European;

-----------------------------------------
-- Drop old schema
-----------------------------------------

-- Drop Table Statements
DROP TABLE IF EXISTS report;
DROP TABLE IF EXISTS bookmarks;
DROP TABLE IF EXISTS mention;
DROP TABLE IF EXISTS comment_likes;
DROP TABLE IF EXISTS post_likes;
DROP TABLE IF EXISTS group_member;
DROP TABLE IF EXISTS group_chat;
DROP TABLE IF EXISTS follow_request;
DROP TABLE IF EXISTS message;
DROP TABLE IF EXISTS notification;
DROP TABLE IF EXISTS comment;
DROP TABLE IF EXISTS post;
DROP TABLE IF EXISTS users;

------------------------------
-- Tables
------------------------------

-- R01: users
CREATE TABLE users (
    userId INT PRIMARY KEY,
    username VARCHAR(255) UNIQUE NOT NULL,
    email VARCHAR(255) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    name VARCHAR(255) NOT NULL,
    bio TEXT,
    private BOOLEAN DEFAULT false
);

-- R02: post
CREATE TABLE post (
    postId INT PRIMARY KEY,
    owner_id INT REFERENCES users(userId) NOT NULL,
    image TEXT,
    content TEXT,
    date DATE NOT NULL CHECK (date <= CURRENT_DATE)
);

-- R03: comment
CREATE TABLE comment (
    id INT PRIMARY KEY,
    author_id INT REFERENCES users(userId) NOT NULL,
    post_id INT REFERENCES post(postId) NOT NULL,
    content TEXT,
    date DATE NOT NULL CHECK (date <= CURRENT_DATE),
    previous INT DEFAULT NULL
);

-- R04: notification
CREATE TABLE notification (
    id INT PRIMARY KEY,
    date DATE NOT NULL CHECK (date <= CURRENT_DATE),
    notified_user INT NOT NULL,
    notification_type VARCHAR(50) NOT NULL CHECK (
        (notification_type IN ('liked_comment', 'reply_comment') AND post_id IS NULL AND group_id IS NULL) OR
        (notification_type IN ('request_follow', 'started_following', 'accept_follow') AND comment_id IS NULL AND post_id IS NULL AND   group_id IS NULL) OR
        (notification_type IN ('joined_group', 'group_invite') AND comment_id IS NULL AND post_id IS NULL) OR
        (notification_type IN ('liked_post', 'comment_post') AND comment_id IS NULL AND group_id IS NULL)
    ),
    comment_id INT,
    post_id INT,
    group_id INT,
    viewed BOOLEAN NOT NULL DEFAULT false,
    FOREIGN KEY (notified_user) REFERENCES users(id),
    FOREIGN KEY (comment_id) REFERENCES comment(id),
    FOREIGN KEY (post_id) REFERENCES post(id),
    FOREIGN KEY (group_id) REFERENCES group_chat(id)
);


-- R11: group_chat
CREATE TABLE group_chat (
    group_id INT PRIMARY KEY,
    owner_id INT REFERENCES users(userId) NOT NULL,
    name VARCHAR(255) NOT NULL,
    description TEXT
);

-- R09: message
CREATE TABLE message (
    id INT PRIMARY KEY,
    emitter_id INT REFERENCES users(userId) NOT NULL,
    group_id INT REFERENCES group_chat(group_id) NOT NULL,
    content TEXT NOT NULL,
    date DATE NOT NULL CHECK (date <= CURRENT_DATE),
    viewed BOOLEAN DEFAULT false
);

-- R10: follow_request
CREATE TABLE follow_request (
    req_id INT REFERENCES users(userId) NOT NULL,
    rcv_id INT REFERENCES users(userId) NOT NULL,
    date DATE NOT NULL CHECK (date <= CURRENT_DATE),
    status VARCHAR(255) NOT NULL CHECK (status IN ('follow_request_status')),
    PRIMARY KEY (req_id, rcv_id)
);


-- R12: group_member
CREATE TABLE group_member (
    user_id INT REFERENCES users(userId),
    group_id INT REFERENCES group_chat(group_id),
    PRIMARY KEY (user_id, group_id)
);

-- R13: post_likes
CREATE TABLE post_likes (
    user_id INT REFERENCES users(userId),
    post_id INT REFERENCES post(postId),
    PRIMARY KEY (user_id, post_id)
);

-- R14: comment_likes
CREATE TABLE comment_likes (
    user_id INT REFERENCES users(userId),
    comment_id INT REFERENCES comment(id),
    PRIMARY KEY (user_id, comment_id)
);

-- R15: mention
CREATE TABLE mention (
    post_id INT REFERENCES post(postId),
    user_mentioned INT REFERENCES users(userId),
    PRIMARY KEY (post_id, user_mentioned)
);

-- R16: bookmarks
CREATE TABLE bookmarks (
    bookmarked_post INT REFERENCES post(postId),
    user_id INT REFERENCES users(userId),
    PRIMARY KEY (bookmarked_post, user_id)
);

-- R17: report
CREATE TABLE report (
    id INT PRIMARY KEY,
    user_id INT REFERENCES users(userId),
    post_id INT REFERENCES post(postId),
    date DATE NOT NULL CHECK (date <= CURRENT_DATE),
    report_type VARCHAR(255) NOT NULL CHECK (report_type IN ('report_types'))
);

------------------------------
-- INDEXES
------------------------------

CREATE INDEX owner_id_post ON post USING hash (owner_id);

CREATE INDEX author_id_comment ON comment USING hash (author_id);

CREATE INDEX notified_user_notification ON notification USING btree (notified_user);

CREATE INDEX emitter_user_notification ON notification USING btree (emitter_user);

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

CREATE FUNCTION notify_friend_request() RETURNS TRIGGER AS
$BODY$
BEGIN
    INSERT INTO notifications (user_id, message, created_at)
    VALUES (NEW.receiver_id, 'You have a new friend request from ' || NEW.sender_name);
    RETURN NEW;
END
$BODY$
LANGUAGE plpgsql;

CREATE TRIGGER notify_friend_request
AFTER INSERT ON friend_requests
FOR EACH ROW
EXECUTE PROCEDURE notify_friend_request();

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
BEFORE INSERT ON posts
FOR EACH ROW
EXECUTE PROCEDURE reject_inappropriate_posts();

CREATE OR REPLACE FUNCTION prevent_duplicate_post_likes();
RETURNS TRIGGER AS $$
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

-- Set the isolation level to Repeatable Read
SET TRANSACTION ISOLATION LEVEL REPEATABLE READ;

-- Begin the transaction
BEGIN;

-- Create the post and obtain the postId
INSERT INTO post (owner_id, content, date)
VALUES (:ownerId, :content, NOW())
RETURNING postId INTO newPostId;

-- Create mention records for the post by extracting mentions from the content
INSERT INTO mention (post_id, user_mentioned)
SELECT newPostId, regexp_matches(:content, '@([A-Za-z0-9_]+)', 'g');

-- Commit the transaction if everything is successful
COMMIT;

