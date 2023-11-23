--
-- Use a specific schema and set it as default - lbaw23141
--

DROP SCHEMA IF EXISTS lbaw23141 CASCADE;
CREATE SCHEMA IF NOT EXISTS lbaw23141;
SET search_path TO lbaw23141;

--
--DROP the old schema
--
DROP TABLE IF EXISTS notifications CASCADE;
DROP TABLE IF EXISTS messages CASCADE;
DROP TABLE IF EXISTS group_members CASCADE;
DROP TABLE IF EXISTS group_chats CASCADE;
DROP TABLE IF EXISTS reports CASCADE;
DROP TABLE IF EXISTS bookmarks CASCADE;
DROP TABLE IF EXISTS mentions CASCADE;
DROP TABLE IF EXISTS comment_likes CASCADE;
DROP TABLE IF EXISTS post_likes CASCADE;
DROP TABLE IF EXISTS follow_requests CASCADE;
DROP TABLE IF EXISTS comments CASCADE;
DROP TABLE IF EXISTS posts CASCADE;
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
DROP FUNCTION IF EXISTS create_group_owner CASCADE;

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

-- Table: posts (R02)
CREATE TABLE posts (
    post_id SERIAL PRIMARY KEY,
    owner_id INTEGER REFERENCES users(user_id) NOT NULL,
    image TEXT,
    content TEXT,
    created_at TIMESTAMP NOT NULL,
    updated_at TIMESTAMP DEFAULT NULL
);

-- Table: comments (R03)
CREATE TABLE comments (
    comment_id SERIAL PRIMARY KEY,
    author_id INTEGER REFERENCES users(user_id) NOT NULL,
    post_id INTEGER REFERENCES posts(post_id) NOT NULL,
    content TEXT,
    created_at TIMESTAMP NOT NULL,
    updated_at TIMESTAMP DEFAULT NULL,
    previous INTEGER REFERENCES comments(comment_id) DEFAULT NULL
);

-- Table: group_chats (R07)
CREATE TABLE group_chats (
    group_id SERIAL PRIMARY KEY,
    owner_id INTEGER REFERENCES users(user_id) NOT NULL,
    name VARCHAR(255) NOT NULL,
    description TEXT
);

-- Table: messages (R05)
CREATE TABLE messages (
    message_id SERIAL PRIMARY KEY,
    emitter_id INTEGER REFERENCES users(user_id) NOT NULL,
    group_id INTEGER REFERENCES group_chats(group_id) NOT NULL,
    content TEXT NOT NULL,
    date DATE NOT NULL CHECK (date <= CURRENT_DATE),
    viewed BOOLEAN NOT NULL DEFAULT false
);

-- Table: follow_requests (R06)
CREATE TABLE follow_requests (
    req_id INTEGER REFERENCES users(user_id) NOT NULL,
    rcv_id INTEGER REFERENCES users(user_id) NOT NULL,
    date DATE NOT NULL CHECK (date <= CURRENT_DATE),
    status request_status NOT NULL,
	PRIMARY KEY (req_id, rcv_id)
);



-- Table: group_members (R08)
CREATE TABLE group_members (
    user_id INTEGER REFERENCES users(user_id),
    group_id INTEGER REFERENCES group_chats(group_id),
    status request_status NOT NULL,
	PRIMARY KEY (user_id, group_id)
);

-- Table: post_likes (R09)
CREATE TABLE post_likes (
    user_id INTEGER REFERENCES users(user_id),
    post_id INTEGER REFERENCES posts(post_id),
    PRIMARY KEY (user_id, post_id)
);

-- Table: comment_likes (R10)
CREATE TABLE comment_likes (
    user_id INTEGER REFERENCES users(user_id),
    comment_id INTEGER REFERENCES comments(comment_id),
    PRIMARY KEY (user_id, comment_id)
);

-- Table: mentions (R11)
CREATE TABLE mentions (
    post_id INTEGER REFERENCES posts(post_id),
    user_mentioned INTEGER REFERENCES users(user_id),
    PRIMARY KEY (post_id, user_mentioned)
);

-- Table: bookmarks (R12)
CREATE TABLE bookmarks (
    bookmarked_post INTEGER REFERENCES posts(post_id),
    user_id INTEGER REFERENCES users(user_id),
    PRIMARY KEY (bookmarked_post, user_id)
);

-- Table: reports (R13)
CREATE TABLE reports (
    report_id SERIAL PRIMARY KEY,
    user_id INTEGER REFERENCES users(user_id),
    post_id INTEGER REFERENCES posts(post_id),
    date DATE NOT NULL CHECK (date <= CURRENT_DATE),
    report_type report_types NOT NULL
);

-- Table: notifications (R04)
CREATE TABLE notifications (
    notification_id SERIAL PRIMARY KEY,
    date DATE NOT NULL CHECK (date <= CURRENT_DATE),
    notified_user INTEGER REFERENCES users(user_id) NOT NULL,
    message VARCHAR(255),
    notification_type notification_types NOT NULL CHECK (
        (notification_type IN ('liked_comment', 'reply_comment') AND post_id IS NULL AND group_id IS NULL) OR
        (notification_type IN ('request_follow', 'started_following', 'accepted_follow') AND comment_id IS NULL AND post_id IS NULL AND group_id IS NULL) OR
        (notification_type IN ('joined_group', 'group_invite') AND comment_id IS NULL AND post_id IS NULL) OR
        (notification_type IN ('liked_post', 'comment_post') AND comment_id IS NULL AND group_id IS NULL)
    ),
    comment_id INTEGER REFERENCES comments(comment_id),
    post_id INTEGER REFERENCES posts(post_id),
    group_id INTEGER REFERENCES group_chats(group_id),
    viewed BOOLEAN NOT NULL DEFAULT false
);

------------------------------
-- INDEXES
------------------------------

CREATE INDEX owner_id_post ON posts USING hash (owner_id);

CREATE INDEX author_id_comment ON comments USING hash (author_id);

CREATE INDEX notified_user_notification ON notifications USING btree (notified_user);

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

-- Add column to posts to store computed ts_vectors.
ALTER TABLE posts
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

-- Create a trigger before insert or update on posts
CREATE TRIGGER post_search_update
BEFORE INSERT OR UPDATE ON posts
FOR EACH ROW
EXECUTE PROCEDURE post_search_update();

-- Create a GIN index for ts_vectors.
CREATE INDEX search_post ON posts USING GIN (tsvectors);

-- Index 08 --

-- Add column to comments to store computed ts_vectors.
ALTER TABLE comments
ADD COLUMN tsvectors TSVECTOR;

-- Create a function to automatically update ts_vectors.
CREATE FUNCTION comment_search_update() RETURNS TRIGGER AS $$
BEGIN
IF TG_OP = 'INSERT' THEN
    NEW.tsvectors = to_tsvector('portuguese', NEW.content);
END IF;
IF TG_OP = 'UPDATnotificaE' THEN
    IF (NEW.content <> OLD.content) THEN
        NEW.tsvectors = to_tsvector('portuguese', NEW.content);
    END IF;
END IF;
RETURN NEW;
END $$
LANGUAGE plpgsql;

-- Create a trigger before insert or update on comments
CREATE TRIGGER comment_search_update
BEFORE INSERT OR UPDATE ON comments
FOR EACH ROW
EXECUTE PROCEDURE comment_search_update();

-- Create a GIN index for ts_vectors.
CREATE INDEX search_comment ON comments USING GIN (tsvectors);

-- Index 09 --

-- Add column to group_chats to store computed ts_vectors.
ALTER TABLE group_chats
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

-- Create a trigger before insert or update on group_chats
CREATE TRIGGER group_chat_search_update
BEFORE INSERT OR UPDATE ON group_chats
FOR EACH ROW
EXECUTE PROCEDURE group_chat_search_update();

-- Create a GIN index for comments on post 1ts_vectors.
CREATE INDEX search_group_chat ON group_chats USING GIN (tsvectors);

------------------------------
-- TRIGGERS
------------------------------

-------NOTIFY FOLLOW REQUEST TRIGGER--------
CREATE OR REPLACE FUNCTION notify_follow_request() RETURNS TRIGGER AS
$BODY$
DECLARE
    reciever_username TEXT;
    rcv_privacy BOOLEAN;
BEGIN
    SELECT username INTO reciever_username FROM users WHERE user_id = NEW.req_id;
    SELECT private INTO rcv_privacy FROM users WHERE user_id = NEW.rcv_id;

    if rcv_privacy = true THEN
        if NEW.status = 'waiting' THEN
            INSERT INTO notifications (notified_user, message, date, notification_type)
            VALUES (NEW.rcv_id, 'You have a new follow request from ' || reciever_username, CURRENT_DATE, 'request_follow');
        else
            INSERT INTO notifications (notified_user, message, date, notification_type, viewed)
            VALUES (NEW.rcv_id, 'You have a new follow request from ' || reciever_username, CURRENT_DATE, 'request_follow', TRUE);
        end if;
    ELSE
        INSERT INTO notifications (notified_user, message, date, notification_type)
            VALUES (NEW.rcv_id, reciever_username || ' started following you.', CURRENT_DATE, 'started_following');
    end if;
    
    RETURN NEW;
END
$BODY$
LANGUAGE plpgsql;

CREATE TRIGGER notify_follow_request
AFTER INSERT ON follow_requests
FOR EACH ROW
EXECUTE PROCEDURE notify_follow_request();

-------NOTIFY LIKED COMMENT TRIGGER--------
CREATE OR REPLACE FUNCTION notify_liked_comment() RETURNS TRIGGER AS
$BODY$
DECLARE
    user_who_liked TEXT;
    notified_user INTEGER;
    comment_content TEXT;

BEGIN
    SELECT username INTO user_who_liked FROM users WHERE user_id = NEW.user_id;
    SELECT content, author_id INTO comment_content, notified_user FROM comments WHERE comment_id = NEW.comment_id; 
    IF NEW.user_id <> notified_user THEN
    INSERT INTO notifications (notified_user, message, date, notification_type, comment_id)
    VALUES (notified_user, user_who_liked || ' liked your comment: ' || comment_content, CURRENT_DATE, 'liked_comment', NEW.comment_id);
    END IF;
    RETURN NEW;
END
$BODY$
LANGUAGE plpgsql;

CREATE TRIGGER notify_liked_comment
AFTER INSERT ON comment_likes
FOR EACH ROW
EXECUTE PROCEDURE notify_liked_comment();


-------NOTIFY COMMENT REPLY TRIGGER--------
CREATE OR REPLACE FUNCTION notify_comment_reply() RETURNS TRIGGER AS
$BODY$
DECLARE
    user_who_commented TEXT;
    author_previous_comment INTEGER;


BEGIN
    SELECT username INTO user_who_commented FROM users WHERE user_id = NEW.author_id;

    SELECT author_id INTO author_previous_comment FROM comments WHERE comment_id = NEW.previous; 

    IF NEW.previous IS NOT NULL AND NEW.author_id <> author_previous_comment THEN


    INSERT INTO notifications (notified_user, message, date, notification_type, comment_id)
    VALUES (author_previous_comment, user_who_commented || ' replied your comment with: ' || NEW.content, CURRENT_DATE, 'reply_comment', NEW.comment_id);

    END IF;

    RETURN NEW;
END
$BODY$
LANGUAGE plpgsql;

CREATE TRIGGER notify_comment_reply
AFTER INSERT ON comments
FOR EACH ROW
EXECUTE PROCEDURE notify_comment_reply();

------NOTIFY ACCEPTED FOLLOW TRIGGER-------
CREATE OR REPLACE FUNCTION notify_accepted_follow() RETURNS TRIGGER AS
$BODY$
DECLARE
    reciever_username TEXT;
    rcv_privacy BOOLEAN;
BEGIN
    SELECT username INTO reciever_username FROM users WHERE user_id = NEW.rcv_id;
    SELECT private INTO rcv_privacy FROM users WHERE user_id = NEW.rcv_id;

    if rcv_privacy = true AND NEW.status = 'accepted' THEN
            INSERT INTO notifications (notified_user, message, date, notification_type)
            VALUES (NEW.req_id, reciever_username || ' accepted your follow request.', CURRENT_DATE, 'accepted_follow');
    end if;
    
    RETURN NEW;
END
$BODY$
LANGUAGE plpgsql;

CREATE TRIGGER notify_accepted_follow
AFTER INSERT OR UPDATE ON follow_requests
FOR EACH ROW
EXECUTE PROCEDURE notify_accepted_follow();

-------NOTIFY JOINED GROUP TRIGGER---------
CREATE OR REPLACE FUNCTION notify_joined_group() RETURNS TRIGGER AS
$BODY$
DECLARE
    group_owner INTEGER;
    group_name TEXT;
    member_username TEXT;
BEGIN
    SELECT owner_id, name INTO group_owner, group_name FROM group_chats WHERE group_id = NEW.group_id;
    SELECT username INTO member_username FROM users WHERE user_id = NEW.user_id;

    IF NEW.status = 'accepted' AND group_owner <> NEW.user_id THEN
        INSERT INTO notifications (notified_user, message, date, notification_type, group_id)
            VALUES (group_owner, member_username || ' joined group ' || group_name, CURRENT_DATE, 'joined_group', NEW.group_id);
    END IF;
    RETURN NEW;
END
$BODY$
LANGUAGE plpgsql;

CREATE TRIGGER notify_joined_group
AFTER INSERT OR UPDATE ON group_members
FOR EACH ROW
EXECUTE PROCEDURE notify_joined_group();

-------NOTIFY GROUP INVITE TRIGGER---------
CREATE OR REPLACE FUNCTION notify_group_invite() RETURNS TRIGGER AS
$BODY$
DECLARE
    group_owner INTEGER;
    group_name TEXT;
BEGIN
    SELECT owner_id, name INTO group_owner, group_name FROM group_chats WHERE group_id = NEW.group_id;

    if group_owner <> NEW.user_id THEN
    if NEW.status = 'waiting' THEN
        INSERT INTO notifications (notified_user, message, date, notification_type, group_id)
        VALUES (NEW.user_id, 'You where invited to join group ' || group_name, CURRENT_DATE, 'group_invite', NEW.group_id);
    else
        INSERT INTO notifications (notified_user, message, date, notification_type, group_id, viewed)
        VALUES (NEW.user_id, 'You where invited to join group ' || group_name, CURRENT_DATE, 'group_invite', NEW.group_id, TRUE);
    end if;
    end if;
    
    RETURN NEW;
END
$BODY$
LANGUAGE plpgsql;

CREATE TRIGGER notify_group_invite
AFTER INSERT ON group_members
FOR EACH ROW
EXECUTE PROCEDURE notify_group_invite();

--------NOTIFY LIKED POST TRIGGER----------
CREATE OR REPLACE FUNCTION notify_liked_post() RETURNS TRIGGER AS
$BODY$
DECLARE
    user_who_liked TEXT;
    notified_user INTEGER;

BEGIN
    SELECT username INTO user_who_liked FROM users WHERE user_id = NEW.user_id;
    SELECT owner_id INTO notified_user FROM posts WHERE post_id = NEW.post_id; 
    IF NEW.user_id <> notified_user THEN
    INSERT INTO notifications (notified_user, message, date, notification_type, post_id)
    VALUES (notified_user, user_who_liked || ' liked your post.', CURRENT_DATE, 'liked_post', NEW.post_id);
    END IF;
    RETURN NEW;
END
$BODY$
LANGUAGE plpgsql;

CREATE TRIGGER notify_liked_post
AFTER INSERT ON post_likes
FOR EACH ROW
EXECUTE PROCEDURE notify_liked_post();


--------NOTIFY COMMENT POST TRIGGER--------
CREATE OR REPLACE FUNCTION notify_comment_post() RETURNS TRIGGER AS
$BODY$
DECLARE
    user_who_commented TEXT;
    notified_user INTEGER;

BEGIN
    SELECT username INTO user_who_commented FROM users WHERE user_id = NEW.author_id;
    SELECT owner_id INTO notified_user FROM posts WHERE post_id = NEW.post_id;

    IF NEW.author_id <> notified_user THEN
    INSERT INTO notifications (notified_user, message, date, notification_type, post_id)
    VALUES (notified_user, user_who_commented || ' commented in your post.', CURRENT_DATE, 'comment_post', NEW.post_id);
    END IF;
    RETURN NEW;
END
$BODY$
LANGUAGE plpgsql;

CREATE TRIGGER notify_comment_post
AFTER INSERT ON comments
FOR EACH ROW
EXECUTE PROCEDURE notify_comment_post();

------REJECT INAPPROPRIATE POSTS TRIGGER------
CREATE OR REPLACE FUNCTION reject_inappropriate_posts() RETURNS TRIGGER AS
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

------PREVENT DUPLICATE POST LIKES TRIGGER------
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

------ENFORCE MESSAGE SENDER MEMBERSHIP TRIGGER------
CREATE OR REPLACE FUNCTION enforce_message_sender_membership()
RETURNS TRIGGER AS $$
BEGIN
    IF NOT EXISTS (
        SELECT 1
        FROM group_members
        WHERE user_id = NEW.emitter_id AND group_id = NEW.group_id AND status='accepted'
    ) THEN
    RAISE EXCEPTION 'User is not a member of the group chat.';
    END IF;
    RETURN NEW;
END;
$$ LANGUAGE plpgsql;

CREATE TRIGGER check_message_sender_membership
BEFORE INSERT ON messages
FOR EACH ROW
EXECUTE FUNCTION enforce_message_sender_membership();


------ENFORCE COMMENT PRIVACY TRIGGER------
CREATE OR REPLACE FUNCTION enforce_comment_privacy()
RETURNS TRIGGER AS $$
DECLARE
    post_owner_private BOOLEAN;
    post_owner_id INTEGER;

BEGIN
    SELECT owner_id, private INTO post_owner_id, post_owner_private FROM posts INNER JOIN users ON posts.owner_id = users.user_id WHERE post_id = NEW.post_id;

    IF post_owner_private = true  AND NOT EXISTS (
            SELECT 1
            FROM follow_requests
            WHERE req_id = NEW.author_id
            AND rcv_id = post_owner_id
            AND status = 'accepted'
        )
     THEN
    RAISE EXCEPTION 'User is not allowed to comment on this post.';
    END IF;
    RETURN NEW;
END;
$$ LANGUAGE plpgsql;

CREATE TRIGGER check_comment_privacy
BEFORE INSERT ON comments
FOR EACH ROW
EXECUTE FUNCTION enforce_comment_privacy();

------PREVENT SELF FOLLOW TRIGGER------
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
BEFORE INSERT ON follow_requests
FOR EACH ROW
EXECUTE FUNCTION prevent_self_follow();

------PREVENT DUPLICATE GROUP MEMBERSHIP TRIGGER------
CREATE OR REPLACE FUNCTION prevent_duplicate_group_membership()
RETURNS TRIGGER AS $$
BEGIN
    IF EXISTS (
        SELECT 1
        FROM group_members
        WHERE user_id = NEW.user_id AND group_id = NEW.group_id
    ) THEN
    RAISE EXCEPTION 'User is already a member of this group chat.';
    END IF;
    RETURN NEW;
END;
$$ LANGUAGE plpgsql;

CREATE TRIGGER enforce_unique_group_membership
BEFORE INSERT ON group_members
FOR EACH ROW
EXECUTE FUNCTION prevent_duplicate_group_membership();

------PREVENT DUPLICATE FOLLOW REQUEST TRIGGER------
CREATE OR REPLACE FUNCTION prevent_duplicate_follow_request()
RETURNS TRIGGER AS $$
BEGIN
    IF EXISTS (
        SELECT 1
        FROM follow_requests
        WHERE req_id = NEW.req_id AND rcv_id = NEW.rcv_id
        AND (status = 'waiting' OR status = 'accepted')
    ) THEN
    RAISE EXCEPTION 'User has already requested to follow or is already following.';
    END IF;
    RETURN NEW;
END;
$$ LANGUAGE plpgsql;

CREATE TRIGGER enforce_unique_follow_request
BEFORE INSERT ON follow_requests
FOR EACH ROW
EXECUTE FUNCTION prevent_duplicate_follow_request();

-----CREATE GROUP OWNER TRIGGER-----
CREATE OR REPLACE FUNCTION create_group_owner() RETURNS TRIGGER AS
$BODY$
BEGIN
    INSERT INTO group_members (user_id, group_id, status)
    VALUES (NEW.owner_id, NEW.group_id, 'accepted');
    RETURN NEW;
END
$BODY$
LANGUAGE plpgsql;

CREATE TRIGGER create_group_owner
AFTER INSERT ON group_chats
FOR EACH ROW
EXECUTE PROCEDURE create_group_owner();

------ENSURE OWNER IS MEMBER TRIGGER------
CREATE OR REPLACE FUNCTION ensure_owner_is_member()
RETURNS TRIGGER AS $$
BEGIN
    IF NOT EXISTS (
        SELECT 1
        FROM group_members
        WHERE user_id = NEW.owner_id AND group_id = NEW.group_id AND status = 'accepted'
    ) THEN
    RAISE EXCEPTION 'The group chat owner must be a member of the group chat.';
    END IF;
    RETURN NEW;
END;
$$ LANGUAGE plpgsql;

CREATE TRIGGER ensure_owner_is_member
BEFORE UPDATE ON group_chats
FOR EACH ROW
EXECUTE FUNCTION ensure_owner_is_member();

------DELETE COMMENT CASCADE------
CREATE OR REPLACE FUNCTION delete_comment()
RETURNS TRIGGER AS $$
BEGIN
    -- Delete comment likes associated with the comment
    DELETE FROM comment_likes WHERE comment_id = OLD.comment_id;

    -- Delete comments with the same previous ID
    DELETE FROM comments WHERE previous = OLD.comment_id;

    DELETE FROM notifications WHERE comment_id = OLD.comment_id;

    RETURN OLD;
END;
$$ LANGUAGE plpgsql;

CREATE TRIGGER trigger_delete_comment
BEFORE DELETE ON comments
FOR EACH ROW    
EXECUTE FUNCTION delete_comment();

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
    INSERT INTO posts (owner_id, content, date)
    VALUES (owner_id, post_content, NOW())
    RETURNING post_id INTO newpost_id;

    -- Create mentions records for the post by extracting mentions from the content
    INSERT INTO mentions (post_id, user_mentioned)
    SELECT newpost_id, user_id
    FROM users
    WHERE regexp_matches(post_content, '@([A-Za-z0-9_]+)', 'g') IS NOT NULL;

    -- Commit the transaction if everything is successful
    COMMIT;

    -- Return nothing (VOID)
    RETURN;
END;
$$ LANGUAGE plpgsql;


------------------------------
-- INSERTS
------------------------------

-- Insert statements for the 'users' table
INSERT INTO users (username, email, password, name, bio, private, user_type)
VALUES
    ('john_doe', 'john.doe@example.com', '$2a$12$goYowY.ccXyLR8so94t3y.jEXtVs7.IDFworUJqqxIGmMd4XhrITa', 'John Doe', 'Computer Science student at University X', false, 'normal_user'),
    ('alice_smith', 'alice.smith@example.com', '$2a$12$GE0k/yA1x88VMc48xmgiEeLWnbDCxqDuyUMm3RT9QBEj.GdP0BzjW', 'Alice Smith', 'Mathematics enthusiast', true, 'normal_user'),
    ('prof_jones', 'prof.jones@example.com', '$2a$12$hCqEe5DSo019ZZsRY9xFfOpVHlZ642xSjZIxWKx3xmveCOYBMISj6', 'Professor Jones', 'Teaching Physics at University X', false, 'normal_user'),
    ('admin1', 'admin1@example.com', '$2a$12$Pw1yagUYBgvoMqcGGv8McOkRXS4WMO/a6S7RH.HjmmlmQQyPHueMG', 'Admin One', 'Administrator at University X', false, 'admin'),
    ('admin2', 'admin2@example.com', '$2a$12$XQefcocDGnOoS5Cl4Q9Vw.ChpEzIH2Ft4uxD8E9spoVX2ijO2JdZG', 'Admin Two', 'Second Administrator at University X', true, 'admin'),
    ('ta_physics', 'ta_physics@example.com', '$2a$12$5FBTo8/npT5CBquqcxIBduolFG3JsT1l6iLLAUhfInmJ.gfBELiiu', 'TA Physics', 'Teaching Assistant for Physics course', false, 'normal_user'),
    ('ta_math', 'ta_math@example.com', '$2a$12$Cqrwqn4G58jRZdGgOUFH7uM9dt5I5tFWyItYpMquC7If8TndQNIdO', 'TA Math', 'Teaching Assistant for Mathematics course', false, 'normal_user'),
    ('banned_user', 'banned_user@example.com', '$2a$12$ByMwcBrT3hICpLZTbop0XuBHRJ9BOS71olRdbXU92QTXk0jxGfLqi', 'Banned User', 'Account suspended due to violation of university policies', false, 'normal_user'),
    ('banned_user2', 'banned_user2@example.com', '$2a$12$K2UnxG3ulT4HxlZKj/tFr.AJ3zimcjLPTQDHgKZdP/z6xJlW1ITH2', 'Banned User 2', 'Second account suspended', false, 'normal_user'),
    ('jane_doe', 'jane.doe@example.com', '$2a$12$pL/fXwZkS4vihbITTltNV.fA5G4IQYVrts0Ds2wf9gtKm/VXeK8yO', 'Jane Doe', 'Studying Computer Engineering at University X', false, 'suspended');

-- Insert statements for the 'follow_requests' table
INSERT INTO follow_requests (req_id, rcv_id, date, status)
VALUES
    (1, 2, '2023-10-26', 'waiting'),
    (2, 1, '2023-10-27', 'accepted'),
    (3, 2, '2023-10-28', 'waiting'),
    (4, 3, '2023-10-29', 'accepted'),
    (4, 2, '2023-10-27', 'accepted'),
    (5, 4, '2023-10-30', 'accepted'),
    (6, 5, '2023-10-31', 'waiting'),
    (7, 6, '2023-11-01', 'accepted'),
    (8, 7, '2023-11-02', 'accepted'),
    (9, 8, '2023-11-03', 'accepted'),
    (10, 9, '2023-11-04', 'accepted');

-- Insert statements for the 'posts' table
INSERT INTO posts (owner_id, image, content, created_at, updated_at)
VALUES
    (1, 'image1.jpg', 'Studying late at the library. #CodingAllNight', '2023-10-26 19:30:00', NULL),
    (2, 'image2.jpg', 'Solving complex math problems today!', '2023-10-25 15:45:00', NULL),
    (4, NULL, 'Important announcement for Physics students.', '2023-10-24 12:00:00', NULL),
    (2, 'image4.jpg', 'Just aced my midterms! Feeling great!', '2023-10-23 10:15:00', NULL),
    (7, NULL, 'Excited to join University X! #NewBeginnings', '2023-10-22 08:00:00', NULL),
    (2, 'image6.jpg', 'Another day, another math challenge.', '2023-10-21 16:20:00', NULL),
    (1, 'image7.jpg', 'Exploring campus and meeting new friends.', '2023-10-20 14:30:00', NULL),
    (8, 'image8.jpg', 'Account suspended due to violation of university policies.', '2023-10-19 11:45:00', NULL),
    (9, NULL, 'Second account suspended. Lesson learned.', '2023-10-18 09:00:00', NULL),
    (10, NULL, 'Hello from Joe. #LifeAtUniversityX', '2023-10-17 07:15:00', NULL);

-- Insert statements for the 'comment' table
INSERT INTO comments (author_id, post_id, content, created_at, previous)
VALUES
    (1, 1, 'Late-night coding sessions are the best!', '2023-10-26 20:00:00', NULL),
    (4, 2, 'Great job on the math problems!', '2023-10-25 16:00:00', NULL),
    (4, 8, 'Agreed, the decision was necessary.', '2023-10-24 12:30:00', NULL),
    (3, 9, 'Unfortunately, yes. Lets hope for better days.', '2023-10-23 11:00:00', NULL),
    (3, 9, 'Its a long story. Lets chat offline.', '2023-10-23 11:30:00', NULL),
    (10, 10, 'Greetings, Joe! Hows university life treating you?', '2023-10-17 08:00:00', NULL),
    (2, 1, 'Coding all night is the key to success!', '2023-10-26 20:15:00', 1);


-- Insert statements for the 'group_chats' table
INSERT INTO group_chats (owner_id, name, description)
VALUES
    (1, 'Group One', 'Description for Group One'),
    (2, 'Group Two', 'Description for Group Two'),
    (3, 'Group Three', 'Description for Group Three'),
    (4, 'Group Four', 'Description for Group Four'),
    (5, 'Group Five', 'Description for Group Five'),
    (6, 'Group Six', 'Description for Group Six'),
    (7, 'Group Seven', 'Description for Group Seven'),
    (8, 'Group Eight', 'Description for Group Eight'),
    (9, 'Group Nine', 'Description for Group Nine'),
    (10, 'Joe Group', 'For Joe lovers.');

-- Insert statements for the 'group_members' table
INSERT INTO group_members (user_id, group_id, status)
VALUES
    (2, 1, 'rejected'),
    (3, 2, 'accepted'),
    (4, 2, 'accepted'),
    (5, 3, 'accepted'),
    (6, 3, 'waiting'),
    (7, 4, 'rejected'),
    (8, 4, 'accepted'),
    (9, 10, 'accepted');
    

-- Insert statements for the 'message' table
INSERT INTO messages (emitter_id, group_id, content, date, viewed)
VALUES
    (1, 1, 'Message in Group One by User One', '2023-10-26', false),
    (2, 2, 'Message in Group Two by User Two', '2023-10-25', true),
    (3, 3, 'Message in Group Three by User Three', '2023-10-22', true),
    (4, 4, 'Message in Group Four by User Four', '2023-10-21', true),
    (5, 5, 'Message in Group Five by User Five', '2023-10-20', true),
    (6, 6, 'Message in Group Six by User Six', '2023-10-19', true),
    (7, 7, 'Message in Group Seven by User Seven', '2023-10-18', true),
    (8, 8, 'Message in Group Eight by User Eight', '2023-10-17', true);


-- Insert statements for the 'post_likes' table
INSERT INTO post_likes (user_id, post_id)
VALUES
    (1, 1),
    (2, 1),
    (3, 2),
    (4, 2),
    (5, 3),
    (6, 4),
    (7, 10),
    (8, 10),
    (9, 5),
    (10, 5);

-- Insert statements for the 'comment_likes' table
INSERT INTO comment_likes (user_id, comment_id)
VALUES
    (1, 1),
    (2, 1),
    (3, 3),
    (5, 5),
    (7, 7);

-- Insert statements for the 'mentions' table
INSERT INTO mentions (post_id, user_mentioned)
VALUES
    (1, 2),
    (2, 1),
    (3, 10),
    (4, 3),
    (5, 4),
    (6, 1),
    (7, 2),
    (8, 9),
    (9, 10),
    (10, 1);

-- Insert statements for the 'bookmarks' table
INSERT INTO bookmarks (bookmarked_post, user_id)
VALUES
    (1, 1),
    (2, 2),
    (3, 3),
    (4, 4),
    (5, 5),
    (6, 6),
    (7, 7),
    (8, 8),
    (9, 9),
    (10, 10);

-- Insert statements for the 'reports' table
INSERT INTO reports (user_id, post_id, date, report_type)
VALUES
    (1, 1, '2023-10-26', 'spam'),
    (2, 1, '2023-10-25', 'inappropriate_content'),
    (8, 8, '2023-10-24', 'inappropriate_content'),
    (8, 8, '2023-10-23', 'spam'),
    (8, 8, '2023-10-22', 'inappropriate_content'),
    (9, 9, '2023-10-21', 'spam'),
    (9, 9, '2023-10-20', 'inappropriate_content'),
    (9, 9, '2023-10-19', 'spam'),
    (1, 1, '2023-10-18', 'inappropriate_content'),
    (10, 10, '2023-10-17', 'spam');


