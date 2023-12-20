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
CREATE TYPE user_types AS ENUM ('normal_user', 'admin', 'suspended', 'deleted');
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
    avatar VARCHAR(255) DEFAULT 'default_avatar.png',
    bio TEXT,
    private BOOLEAN NOT NULL DEFAULT false,
    user_type user_types NOT NULL
);

-- Table: posts (R02)
CREATE TABLE posts (
    post_id SERIAL PRIMARY KEY,
    owner_id INTEGER REFERENCES users(user_id) ON DELETE CASCADE NOT NULL,
    image TEXT,
    content TEXT,
    created_at TIMESTAMP NOT NULL,
    updated_at TIMESTAMP DEFAULT NULL
);

-- Table: comments (R03)
CREATE TABLE comments (
    comment_id SERIAL PRIMARY KEY,
    author_id INTEGER REFERENCES users(user_id) ON DELETE CASCADE NOT NULL,
    post_id INTEGER REFERENCES posts(post_id) ON DELETE CASCADE NOT NULL,
    content TEXT,
    created_at TIMESTAMP NOT NULL,
    updated_at TIMESTAMP DEFAULT NULL,
    previous INTEGER REFERENCES comments(comment_id) ON DELETE CASCADE DEFAULT NULL
);

-- Table: group_chats (R07)
CREATE TABLE group_chats (
    group_id SERIAL PRIMARY KEY,
    owner_id INTEGER REFERENCES users(user_id) ON DELETE CASCADE NOT NULL,
    name VARCHAR(255) NOT NULL,
    description TEXT
);

-- Table: messages (R05)
CREATE TABLE messages (
    message_id SERIAL PRIMARY KEY,
    emitter_id INTEGER REFERENCES users(user_id) ON DELETE CASCADE NOT NULL,
    group_id INTEGER REFERENCES group_chats(group_id) ON DELETE CASCADE NOT NULL,
    content TEXT NOT NULL,
    date DATE NOT NULL CHECK (date <= CURRENT_DATE),
    viewed BOOLEAN NOT NULL DEFAULT false
);

-- Table: follow_requests (R06)
CREATE TABLE follow_requests (
    req_id INTEGER REFERENCES users(user_id) ON DELETE CASCADE NOT NULL,
    rcv_id INTEGER REFERENCES users(user_id) ON DELETE CASCADE NOT NULL,
    date DATE NOT NULL CHECK (date <= CURRENT_DATE),
    status request_status NOT NULL,
	PRIMARY KEY (req_id, rcv_id)
);

-- Table: group_members (R08)
CREATE TABLE group_members (
    user_id INTEGER REFERENCES users(user_id) ON DELETE CASCADE,
    group_id INTEGER REFERENCES group_chats(group_id) ON DELETE CASCADE,
    status request_status NOT NULL,
	PRIMARY KEY (user_id, group_id)
);

-- Table: post_likes (R09)
CREATE TABLE post_likes (
    user_id INTEGER REFERENCES users(user_id) ON DELETE CASCADE,
    post_id INTEGER REFERENCES posts(post_id) ON DELETE CASCADE,
    PRIMARY KEY (user_id, post_id)
);

-- Table: comment_likes (R10)
CREATE TABLE comment_likes (
    user_id INTEGER REFERENCES users(user_id) ON DELETE CASCADE,
    comment_id INTEGER REFERENCES comments(comment_id) ON DELETE CASCADE,
    PRIMARY KEY (user_id, comment_id)
);

-- Table: mentions (R11)
CREATE TABLE mentions (
    post_id INTEGER REFERENCES posts(post_id) ON DELETE CASCADE,
    user_mentioned INTEGER REFERENCES users(user_id) ON DELETE CASCADE,
    PRIMARY KEY (post_id, user_mentioned)
);

-- Table: bookmarks (R12)
CREATE TABLE bookmarks (
    bookmarked_post INTEGER REFERENCES posts(post_id) ON DELETE CASCADE,
    user_id INTEGER REFERENCES users(user_id) ON DELETE CASCADE,
    PRIMARY KEY (bookmarked_post, user_id)
);

-- Table: reports (R13)
CREATE TABLE reports (
    report_id SERIAL PRIMARY KEY,
    user_id INTEGER REFERENCES users(user_id) ON DELETE CASCADE,
    post_id INTEGER REFERENCES posts(post_id) ON DELETE CASCADE,
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
    comment_id INTEGER REFERENCES comments(comment_id) ON DELETE CASCADE,
    post_id INTEGER REFERENCES posts(post_id) ON DELETE CASCADE,
    group_id INTEGER REFERENCES group_chats(group_id) ON DELETE CASCADE,
    user_id INTEGER REFERENCES users(user_id) ON DELETE CASCADE,
    viewed BOOLEAN NOT NULL DEFAULT false
);

CREATE TABLE password_resets (
    email VARCHAR(255) NOT NULL,
    token VARCHAR(255) NOT NULL,
    created_at TIMESTAMP NOT NULL,
    PRIMARY KEY (email, token)
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
        INSERT INTO notifications (notified_user, message, date, notification_type, user_id)
            VALUES (NEW.rcv_id, reciever_username || ' started following you.', CURRENT_DATE, 'started_following', NEW.req_id);
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

    -- Check if the post is private and the commenter is not the owner of the post.
    IF post_owner_private = true AND NEW.author_id != post_owner_id AND NOT EXISTS (
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
INSERT INTO users (username, email, password, name, bio, private, user_type, avatar)
VALUES
    ('john_doe', 'john.doe@example.com', '$2a$12$GE0k/yA1x88VMc48xmgiEeLWnbDCxqDuyUMm3RT9QBEj.GdP0BzjW', 'John Doe', 'Computer Science student at University X', false, 'normal_user', '1702594210.jpg'),
    ('alice_smith', 'alice.smith@example.com', '$2a$12$GE0k/yA1x88VMc48xmgiEeLWnbDCxqDuyUMm3RT9QBEj.GdP0BzjW', 'Alice Smith', 'Mathematics enthusiast', true, 'normal_user', '1702594395.jpg'),
    ('prof_jones', 'prof.jones@example.com', '$2a$12$hCqEe5DSo019ZZsRY9xFfOpVHlZ642xSjZIxWKx3xmveCOYBMISj6', 'Professor Jones', 'Teaching Physics at University X', false, 'normal_user', '1702594478.jpg'),
    ('gcostell0', 'gcostell0@simplemachines.org', '$2a$12$Pw1yagUYBgvoMqcGGv8McOkRXS4WMO/a6S7RH.HjmmlmQQyPHueMG', 'Glynis Costell', 'Administrator at University X', false, 'admin', '1702596523.jpg'),
    ('wleftwich1', 'wleftwich1@howstuffworks.com', '$2a$12$XQefcocDGnOoS5Cl4Q9Vw.ChpEzIH2Ft4uxD8E9spoVX2ijO2JdZG', 'Wittie Leftwich', 'Second Administrator at University X', true, 'admin', '1702596680.jpg'),
    ('fcassel2', 'fcassel2@thetimes.co.uk', '$2a$12$5FBTo8/npT5CBquqcxIBduolFG3JsT1l6iLLAUhfInmJ.gfBELiiu', 'Fernanda Cassel', 'Teaching Assistant for Physics course', false, 'normal_user', '1702596806.jpg'),
    ('ssetterington3', 'ssetterington3@nymag.com', '$2a$12$Cqrwqn4G58jRZdGgOUFH7uM9dt5I5tFWyItYpMquC7If8TndQNIdO', 'Sue Setterington', 'Teaching Assistant for Mathematics course', false, 'normal_user', '1702596887.jpg'),
    ('mloudyan4', 'mloudyan4@wikimedia.org', '$2a$12$ByMwcBrT3hICpLZTbop0XuBHRJ9BOS71olRdbXU92QTXk0jxGfLqi', 'Madelyn Loudyan', 'Account suspended due to violation of university policies', false, 'normal_user', '1702596982.jpg'),
    ('dsesons5', 'dsesons5@webmd.com', '$2a$12$K2UnxG3ulT4HxlZKj/tFr.AJ3zimcjLPTQDHgKZdP/z6xJlW1ITH2', 'Dasie Sesons', 'Second account suspended', false, 'normal_user', '1702597147.jpg'),
    ('jane_doe', 'jane.doe@example.com', '$2a$12$pL/fXwZkS4vihbITTltNV.fA5G4IQYVrts0Ds2wf9gtKm/VXeK8yO', 'Jane Doe', 'Studying Computer Engineering at University X', false, 'normal_user', '1702597217.jpg');

INSERT INTO users (username, email, password, name, bio, private, user_type)
VALUES
    ('jwoolhouse0', 'jwoolhouse0@amazon.co.uk', '$2a$12$OmNSttli8fD9ykDcaXZFA.cZOnxzLrMuU/jY5dTb14Fva9HC8UPFm', 'Juliane Woolhouse', 'Fully-configurable scalable instruction set', true, 'normal_user'),
    ('lsimonds1', 'lsimonds1@cdc.gov', '$2a$12$OmNSttli8fD9ykDcaXZFA.cZOnxzLrMuU/jY5dTb14Fva9HC8UPFm', 'Lesley Simonds', 'Re-contextualized needs-based firmware', false, 'suspended'),
    ('gfairney2', 'gfairney2@privacy.gov.au', '$2a$12$OmNSttli8fD9ykDcaXZFA.cZOnxzLrMuU/jY5dTb14Fva9HC8UPFm', 'Georgiana Fairney', 'Multi-layered multi-state budgetary management', true, 'admin'),
    ('lmallord3', 'lmallord3@toplist.cz', '$2a$12$OmNSttli8fD9ykDcaXZFA.cZOnxzLrMuU/jY5dTb14Fva9HC8UPFm', 'Ludovika Mallord', 'Persistent optimizing local area network', true, 'normal_user'),
    ('bscutts4', 'bscutts4@facebook.com', '$2a$12$OmNSttli8fD9ykDcaXZFA.cZOnxzLrMuU/jY5dTb14Fva9HC8UPFm', 'Boy Scutts', 'Streamlined uniform strategy', true, 'suspended'),
    ('abryns5', 'abryns5@go.com', '$2a$12$OmNSttli8fD9ykDcaXZFA.cZOnxzLrMuU/jY5dTb14Fva9HC8UPFm', 'Ash Bryns', 'Re-engineered actuating customer loyalty', true, 'suspended'),
    ('dhallam6', 'dhallam6@friendfeed.com', '$2a$12$OmNSttli8fD9ykDcaXZFA.cZOnxzLrMuU/jY5dTb14Fva9HC8UPFm', 'Dmitri Hallam', 'Seamless needs-based productivity', false, 'admin'),
    ('sazam7', 'sazam7@ihg.com', '$2a$12$OmNSttli8fD9ykDcaXZFA.cZOnxzLrMuU/jY5dTb14Fva9HC8UPFm', 'Selma Azam', 'Organic bi-directional customer loyalty', true, 'admin'),
    ('mwemes8', 'mwemes8@nhs.uk', '$2a$12$OmNSttli8fD9ykDcaXZFA.cZOnxzLrMuU/jY5dTb14Fva9HC8UPFm', 'Mira Wemes', 'Expanded methodical support', true, 'normal_user'),
    ('koldford9', 'koldford9@foxnews.com', '$2a$12$OmNSttli8fD9ykDcaXZFA.cZOnxzLrMuU/jY5dTb14Fva9HC8UPFm', 'Korney Oldford', 'Diverse needs-based groupware', false, 'admin'),
    ('kkaszpera', 'kkaszpera@reverbnation.com', '$2a$12$OmNSttli8fD9ykDcaXZFA.cZOnxzLrMuU/jY5dTb14Fva9HC8UPFm', 'Kimberly Kaszper', 'Mandatory human-resource implementation', false, 'normal_user'),
    ('amacconnechieb', 'amacconnechieb@prlog.org', '$2a$12$OmNSttli8fD9ykDcaXZFA.cZOnxzLrMuU/jY5dTb14Fva9HC8UPFm', 'Alfons MacConnechie', 'Face to face solution-oriented encoding', true, 'suspended'),
    ('smcturleyc', 'smcturleyc@pbs.org', '$2a$12$OmNSttli8fD9ykDcaXZFA.cZOnxzLrMuU/jY5dTb14Fva9HC8UPFm', 'Sarena McTurley', 'Enhanced fresh-thinking forecast', false, 'suspended'),
    ('maleevyd', 'maleevyd@nps.gov', '$2a$12$OmNSttli8fD9ykDcaXZFA.cZOnxzLrMuU/jY5dTb14Fva9HC8UPFm', 'Milzie Aleevy', 'Secured 24/7 conglomeration', true, 'admin'),
    ('cliversleye', 'cliversleye@examiner.com', '$2a$12$OmNSttli8fD9ykDcaXZFA.cZOnxzLrMuU/jY5dTb14Fva9HC8UPFm', 'Cindelyn Liversley', 'Self-enabling intangible open system', true, 'normal_user'),
    ('kfiremanf', 'kfiremanf@seesaa.net', '$2a$12$OmNSttli8fD9ykDcaXZFA.cZOnxzLrMuU/jY5dTb14Fva9HC8UPFm', 'Katrine Fireman', 'Networked zero tolerance challenge', false, 'suspended'),
    ('evillarg', 'evillarg@state.gov', '$2a$12$OmNSttli8fD9ykDcaXZFA.cZOnxzLrMuU/jY5dTb14Fva9HC8UPFm', 'Ethel Villar', 'Distributed leading edge emulation', true, 'suspended'),
    ('fennionh', 'fennionh@aol.com', '$2a$12$OmNSttli8fD9ykDcaXZFA.cZOnxzLrMuU/jY5dTb14Fva9HC8UPFm', 'Filide Ennion', 'Fully-configurable grid-enabled capacity', true, 'normal_user'),('scheethami', 'scheethami@jiathis.com', '$2a$12$OmNSttli8fD9ykDcaXZFA.cZOnxzLrMuU/jY5dTb14Fva9HC8UPFm', 'Sylas Cheetham', 'Organic global website', true, 'admin'),
    ('dcocksedgej', 'dcocksedgej@utexas.edu', '$2a$12$OmNSttli8fD9ykDcaXZFA.cZOnxzLrMuU/jY5dTb14Fva9HC8UPFm', 'Debera Cocksedge', 'Sharable 5th generation circuit', false, 'suspended'),
    ('gtrendlek', 'gtrendlek@miibeian.gov.cn', '$2a$12$OmNSttli8fD9ykDcaXZFA.cZOnxzLrMuU/jY5dTb14Fva9HC8UPFm', 'Gerry Trendle', 'Re-engineered fault-tolerant alliance', false, 'admin'),
    ('jrosternl', 'jrosternl@exblog.jp', '$2a$12$OmNSttli8fD9ykDcaXZFA.cZOnxzLrMuU/jY5dTb14Fva9HC8UPFm', 'Jennifer Rostern', 'Reduced dynamic ability', false, 'suspended'),
    ('ikeechm', 'ikeechm@bigcartel.com', '$2a$12$OmNSttli8fD9ykDcaXZFA.cZOnxzLrMuU/jY5dTb14Fva9HC8UPFm', 'Ilario Keech', 'Re-contextualized bifurcated knowledge base', false, 'normal_user'),
    ('rdufouren', 'rdufouren@dedecms.com', '$2a$12$OmNSttli8fD9ykDcaXZFA.cZOnxzLrMuU/jY5dTb14Fva9HC8UPFm', 'Roth Dufoure', 'Advanced dynamic access', true, 'admin'),
    ('lmacterrellyo', 'lmacterrellyo@tamu.edu', '$2a$12$OmNSttli8fD9ykDcaXZFA.cZOnxzLrMuU/jY5dTb14Fva9HC8UPFm', 'Leopold MacTerrelly', 'Fundamental regional attitude', true, 'admin'),
    ('wtaylersonp', 'wtaylersonp@wikia.com', '$2a$12$OmNSttli8fD9ykDcaXZFA.cZOnxzLrMuU/jY5dTb14Fva9HC8UPFm', 'Waldemar Taylerson', 'Re-engineered didactic utilisation', false, 'admin'),
    ('gmanieq', 'gmanieq@360.cn', '$2a$12$OmNSttli8fD9ykDcaXZFA.cZOnxzLrMuU/jY5dTb14Fva9HC8UPFm', 'Giorgi Manie', 'User-friendly exuding definition', false, 'suspended'),
    ('cmackailer', 'cmackailer@wordpress.org', '$2a$12$OmNSttli8fD9ykDcaXZFA.cZOnxzLrMuU/jY5dTb14Fva9HC8UPFm', 'Carole MacKaile', 'Progressive user-facing solution', true, 'admin'),
    ('tcutridges', 'tcutridges@godaddy.com', '$2a$12$OmNSttli8fD9ykDcaXZFA.cZOnxzLrMuU/jY5dTb14Fva9HC8UPFm', 'Terry Cutridge', 'Digitized coherent protocol', true, 'suspended'),
    ('abrowert', 'abrowert@list-manage.com', '$2a$12$OmNSttli8fD9ykDcaXZFA.cZOnxzLrMuU/jY5dTb14Fva9HC8UPFm', 'Anallese Brower', 'Enhanced optimal core', false, 'suspended'),
    ('graitu', 'graitu@pcworld.com', '$2a$12$OmNSttli8fD9ykDcaXZFA.cZOnxzLrMuU/jY5dTb14Fva9HC8UPFm', 'Golda Rait', 'Robust client-driven migration', false, 'suspended'),
    ('bjestecov', 'bjestecov@storify.com', '$2a$12$OmNSttli8fD9ykDcaXZFA.cZOnxzLrMuU/jY5dTb14Fva9HC8UPFm', 'Brandea Jesteco', 'Expanded local task-force', false, 'suspended'),
    ('ajoplinw', 'ajoplinw@japanpost.jp', '$2a$12$OmNSttli8fD9ykDcaXZFA.cZOnxzLrMuU/jY5dTb14Fva9HC8UPFm', 'Alisha Joplin', 'Business-focused demand-driven task-force', true, 'admin'),
    ('ncroserx', 'ncroserx@oracle.com', '$2a$12$OmNSttli8fD9ykDcaXZFA.cZOnxzLrMuU/jY5dTb14Fva9HC8UPFm', 'Nettie Croser', 'Networked attitude-oriented ability', false, 'suspended'),
    ('wborrelly', 'wborrelly@deviantart.com', '$2a$12$OmNSttli8fD9ykDcaXZFA.cZOnxzLrMuU/jY5dTb14Fva9HC8UPFm', 'Winifred Borrell', 'Integrated explicit hub', true, 'normal_user'),
    ('ooshavlanz', 'ooshavlanz@slashdot.org', '$2a$12$OmNSttli8fD9ykDcaXZFA.cZOnxzLrMuU/jY5dTb14Fva9HC8UPFm', 'Olympe O''Shavlan', 'Decentralized zero tolerance benchmark', true, 'suspended'),
    ('rmaccague10', 'rmaccague10@dropbox.com', '$2a$12$OmNSttli8fD9ykDcaXZFA.cZOnxzLrMuU/jY5dTb14Fva9HC8UPFm', 'Rosanne MacCague', 'User-friendly neutral service-desk', true, 'suspended'),('cgarlicke11', 'cgarlicke11@sourceforge.net', '$2a$12$OmNSttli8fD9ykDcaXZFA.cZOnxzLrMuU/jY5dTb14Fva9HC8UPFm', 'Cordie Garlicke', 'Automated full-range archive', false, 'normal_user'),
    ('vkershow12', 'vkershow12@wp.com', '$2a$12$OmNSttli8fD9ykDcaXZFA.cZOnxzLrMuU/jY5dTb14Fva9HC8UPFm', 'Vinny Kershow', 'Phased even-keeled function', true, 'admin'),
    ('mdysart13', 'mdysart13@indiatimes.com', '$2a$12$OmNSttli8fD9ykDcaXZFA.cZOnxzLrMuU/jY5dTb14Fva9HC8UPFm', 'Miguelita Dysart', 'Exclusive modular middleware', true, 'normal_user'),
    ('rhevner14', 'rhevner14@ebay.com', '$2a$12$OmNSttli8fD9ykDcaXZFA.cZOnxzLrMuU/jY5dTb14Fva9HC8UPFm', 'Richardo Hevner', 'Phased client-driven circuit', false, 'admin'),
    ('dtrevaskis15', 'dtrevaskis15@friendfeed.com', '$2a$12$OmNSttli8fD9ykDcaXZFA.cZOnxzLrMuU/jY5dTb14Fva9HC8UPFm', 'Donnamarie Trevaskis', 'Persistent dynamic encoding', false, 'normal_user'),
    ('dpolo16', 'dpolo16@ifeng.com', '$2a$12$OmNSttli8fD9ykDcaXZFA.cZOnxzLrMuU/jY5dTb14Fva9HC8UPFm', 'Deni Polo', 'Versatile 24 hour website', false, 'normal_user'),
    ('sdelieu17', 'sdelieu17@etsy.com', '$2a$12$OmNSttli8fD9ykDcaXZFA.cZOnxzLrMuU/jY5dTb14Fva9HC8UPFm', 'Sayers Delieu', 'Upgradable even-keeled model', true, 'admin'),
    ('dcalifornia18', 'dcalifornia18@flavors.me', '$2a$12$OmNSttli8fD9ykDcaXZFA.cZOnxzLrMuU/jY5dTb14Fva9HC8UPFm', 'Dmitri California', 'Future-proofed background software', true, 'admin'),
    ('cgartin19', 'cgartin19@histats.com', '$2a$12$OmNSttli8fD9ykDcaXZFA.cZOnxzLrMuU/jY5dTb14Fva9HC8UPFm', 'Cathee Gartin', 'Re-contextualized context-sensitive frame', false, 'admin'),
    ('nhatto1a', 'nhatto1a@desdev.cn', '$2a$12$OmNSttli8fD9ykDcaXZFA.cZOnxzLrMuU/jY5dTb14Fva9HC8UPFm', 'Norine Hatto', 'Distributed incremental capacity', true, 'suspended');
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
(1, 'images/image1.jpg', 'Hey everyone! Just finished my final exams for the semester. Feeling a mix of relief and exhaustion. Time to catch up on some much-needed sleep and binge-watch my favorite shows. How did your exams go?', '2023-10-26 19:30:00', NULL),
(1, NULL, 'Campus library is truly my second home. 📚 Whether I''m engrossed in the pages of classic literature, diving into the complexities of quantum physics, or exploring the latest developments in artificial intelligence, this sanctuary of knowledge fuels my intellectual curiosity. The serene ambiance, the aroma of aged books, and the gentle hum of focused minds create the perfect backdrop for my academic pursuits. Where''s your go-to study spot?', '2023-10-22 19:30:00', NULL),
(1, NULL, 'Research paper deadline looming. 😬 Any procrastination hacks?', '2023-10-23 19:30:00', NULL),
(1, NULL, 'Attending a fascinating lecture on [topic] tomorrow. Whos in?', '2023-10-21 19:30:00', NULL),
(1, NULL, 'Just joined a campus club. 🤝 Excited to meet new faces!', '2023-10-22 19:30:00', NULL),
(1, NULL, 'Hump day feels. Whats getting you through the week?', '2023-10-21 19:30:00', NULL),
(1, NULL, 'Finished a chapter of my novel. Any bookworms here? 📖', '2023-10-23 19:30:00', NULL),
(1, NULL, 'Campus food review: hit or miss? Share your favorites!', '2023-10-21 19:30:00', NULL),
(1, NULL, 'Who else is counting down to the weekend? 🎉', '2023-10-20 19:30:00', NULL),
(1, NULL, 'Just aced that quiz! 🌟 Hows your day going, fellow students?0', '2023-10-19 19:30:00', NULL),
(2, NULL, 'Exploring new study spots on campus! Found this cozy little corner in the library with the perfect amount of sunlight. What are your favorite study spots around campus? Drop some recommendations!', '2023-10-19 19:30:00', NULL),
(2, 'images/image1.jpg', 'Campus library = my second home. 📚 Wheres your go-to study spot?', '2023-10-17 19:30:00', NULL),
(2, NULL, 'Research paper deadline looming. 😬 Any procrastination hacks?', '2023-10-15 19:30:00', NULL),
(2, NULL, 'Attending a fascinating lecture on [topic] tomorrow. Whos in?', '2023-10-12 19:30:00', NULL),
(2, NULL, 'Just joined a campus club. 🤝 Excited to meet new faces and embark on this journey of shared interests and passions! Whether it''s engaging discussions, collaborative projects, or simply having fun at club events, I''m looking forward to the enriching experiences that await. If you''re part of any campus clubs, I''d love to hear about your favorite moments or any advice for a newcomer like me!', '2023-10-17 19:30:00', NULL),
(2, NULL, 'Hump day feels. Whats getting you through the week?', '2023-10-12 19:30:00', NULL),
(2, NULL, 'Finished a chapter of my novel. Any bookworms here? 📖', '2023-10-10 19:30:00', NULL),
(2, NULL, 'Campus food review: hit or miss? Share your favorites!', '2023-10-14 19:30:00', NULL),
(2, NULL, 'Who else is counting down to the weekend? 🎉', '2023-10-09 19:30:00', NULL),
(2, NULL, 'Just aced that quiz! 🌟 Hows your day going, fellow students?0', '2023-10-06 19:30:00', NULL),
(3, 'images/image1.jpg', 'Just aced my physics project presentation! 🚀 Who else is into science? Lets connect and share our favorite mind-boggling theories or cool experiments. #ScienceLovers', '2023-10-07 19:30:00', NULL),
(3, NULL, 'Campus library = my second home. 📚 Wheres your go-to study spot?', '2023-10-02 19:30:00', NULL),
(3, NULL, 'Research paper deadline looming. 😬 Any procrastination hacks?', '2023-10-03 19:30:00', NULL),
(3, NULL, 'Attending a fascinating lecture on [topic] tomorrow. Whos in?', '2023-10-02 19:30:00', NULL),
(3, NULL, 'Just joined a campus club. 🤝 Excited to meet new faces!', '2023-09-30 19:30:00', NULL),
(3, NULL, 'Hump day feels. Whats getting you through the week?', '2023-09-26 19:30:00', NULL),
(3, NULL, 'Just wrapped up another chapter of my latest novel! 📖 Any fellow bookworms in this literary corner? 🐛 Whether it''s the heart-pounding suspense of a gripping mystery, the enchantment of fantastical realms, or the profound insights from thought-provoking non-fiction, I find solace and joy in the diverse world of books. As I close one chapter, I eagerly anticipate the next adventure between the pages. Let''s embark on this literary journey together! What''s the last book that not only captured your imagination but lingered in your thoughts long after the last page? Share your latest literary gems, and let''s build a reading list brimming with captivating stories!', '2023-09-28 19:30:00', NULL),
(3, NULL, 'Campus food review: hit or miss? Share your favorites!', '2023-09-27 19:30:00', NULL),
(3, NULL, 'Who else is counting down to the weekend? 🎉', '2023-09-20 19:30:00', NULL),
(3, NULL, 'Just aced that quiz! 🌟 Hows your day going, fellow students?0', '2023-09-06 19:30:00', NULL),
(4, 'images/image1.jpg', 'Finished my latest art piece inspired by campus life. 🎨 Check it out and let me know what you think! Also, looking for art buddies. Anyone up for a creative collaboration?', '2023-09-17 19:30:00', NULL),
(4, NULL, 'Campus library = my second home. 📚 Wheres your go-to study spot?', '2023-09-15 19:30:00', NULL),
(4, NULL, 'Research paper deadline looming. 😬 Any procrastination hacks?', '2023-09-18 19:30:00', NULL),
(4, NULL, 'Attending a fascinating lecture on [topic] tomorrow. Whos in?', '2023-09-16 19:30:00', NULL),
(4, NULL, 'Just joined a campus club. 🤝 Excited to meet new faces!', '2023-09-19 19:30:00', NULL),
(4, NULL, 'Hump day feels. Whats getting you through the week?', '2023-09-14 19:30:00', NULL),
(4, NULL, 'Finished a chapter of my novel. Any bookworms here? 📖', '2023-09-17 19:30:00', NULL),
(4, NULL, 'Campus food review: hit or miss? Share your favorites!', '2023-09-11 19:30:00', NULL),
(4, NULL, 'Who else is counting down to the weekend? 🎉', '2023-09-12 19:30:00', NULL),
(4, NULL, 'Just aced that quiz! 🌟 Hows your day going, fellow students?0', '2023-09-04 19:30:00', NULL),
(5, 'images/image1.jpg', 'Exciting news! Just secured an internship with a local startup. Thrilled to apply what Ive learned in class to the real world. Any tips for a budding entrepreneur in the making?', '2023-09-01 19:30:00', NULL),
(5, NULL, 'Campus library = my second home. 📚 Wheres your go-to study spot?', '2023-08-26 19:30:00', NULL),
(5, NULL, 'Research paper deadline looming. 😬 Any procrastination hacks?', '2023-08-29 19:30:00', NULL),
(5, NULL, 'Attending a fascinating lecture on [topic] tomorrow. Whos in?', '2023-08-22 19:30:00', NULL),
(5, NULL, 'Just joined a campus club. 🤝 Excited to meet new faces!', '2023-08-26 19:30:00', NULL),
(5, NULL, 'Hump day feels. Whats getting you through the week?', '2023-08-23 19:30:00', NULL),
(5, NULL, 'Finished a chapter of my novel. Any bookworms here? 📖', '2023-08-22 19:30:00', NULL),
(5, NULL, 'Exploring the vast world of campus cuisine! 🍔🍕 Wondering if it''s a hit or miss? Share your favorite dining spots and must-try dishes! 🌮 From savory bites to sweet delights, every meal is an adventure. Whether you''ve uncovered hidden gems or have cautionary tales about the not-so-appetizing options, let''s dish out the details. Are you team pizza or sushi? Burgers or salads? Excited to hear your campus food adventures!', '2023-08-24 19:30:00', NULL),
(5, NULL, 'Who else is counting down to the weekend? 🎉', '2023-08-21 19:30:00', NULL),
(5, NULL, 'Just aced that quiz! 🌟 Hows your day going, fellow students?0', '2023-08-20 19:30:00', NULL),
(6, 'images/image1.jpg', 'Tough game today, but we gave it our all on the field. Shoutout to my teammates! How do you balance academics and sports? Share your experiences and advice. #StudentAthleteLife', '2023-08-19 19:30:00', NULL),
(6, NULL, 'Campus library = my second home. 📚 Wheres your go-to study spot?', '2023-08-18 19:30:00', NULL),
(6, NULL, 'Research paper deadline looming. 😬 Any procrastination hacks?', '2023-08-17 19:30:00', NULL),
(6, NULL, 'Attending a fascinating lecture on [topic] tomorrow. Whos in?', '2023-08-19 19:30:00', NULL),
(6, NULL, 'Just joined a campus club. 🤝 Excited to meet new faces!', '2023-08-14 19:30:00', NULL),
(6, NULL, 'Midweek blues hitting hard! 🐪✨ Hump day feels have me yearning for that weekend escape. How are you getting through the week? Whether it''s the promise of a delightful cup of coffee each morning, the motivating beats of your favorite playlist, or the thought of a relaxing evening with a good book, we all have our secret weapons to conquer the midweek slump. Share your strategies, rituals, and little joys that make Wednesdays a bit brighter. Let''s build a collective survival guide for those hump day blues!', '2023-08-15 19:30:00', NULL),
(6, NULL, 'Finished a chapter of my novel. Any bookworms here? 📖', '2023-08-12 19:30:00', NULL),
(6, NULL, 'Campus food review: hit or miss? Share your favorites!', '2023-08-13 19:30:00', NULL),
(6, NULL, 'Who else is counting down to the weekend? 🎉', '2023-08-11 19:30:00', NULL),
(6, NULL, 'Just aced that quiz! 🌟 Hows your day going, fellow students?0', '2023-08-10 19:30:00', NULL),
(7, 'images/image1.jpg', 'Currently immersed in this captivating novel I picked up from the campus library. 📚 Any book recommendations? Lets start a virtual book club and discuss our favorite reads!', '2023-08-13 19:30:00', NULL),
(7, NULL, 'Campus library = my second home. 📚 Wheres your go-to study spot?', '2023-08-11 19:30:00', NULL),
(7, NULL, 'Research paper deadline looming. 😬 Any procrastination hacks?', '2023-08-12 19:30:00', NULL),
(7, NULL, 'Attending a fascinating lecture on [topic] tomorrow. Whos in?', '2023-08-10 19:30:00', NULL),
(7, NULL, 'Just joined a campus club. 🤝 Excited to meet new faces!', '2023-08-11 19:30:00', NULL),
(7, NULL, 'Hump day feels. Whats getting you through the week?', '2023-08-13 19:30:00', NULL),
(7, NULL, 'Finished a chapter of my novel. Any bookworms here? 📖', '2023-08-09 19:30:00', NULL),
(7, NULL, 'Campus food review: hit or miss? Share your favorites!', '2023-08-08 19:30:00', NULL),
(7, NULL, 'The weekend is on the horizon! 🎉✨ Who else is counting down the moments until the Friday freedom kicks in? Whether you''re planning a cozy movie night, a spontaneous adventure, or just some well-deserved relaxation, the anticipation is real. Share your weekend plans, favorite activities, or any hidden gems in your city that make weekends special. Let''s make the countdown even more exciting by sharing the excitement and joy that weekends bring! ⏳🌟', '2023-08-06 19:30:00', NULL),
(7, NULL, 'Just aced that quiz! 🌟 Hows your day going, fellow students?0', '2023-08-07 19:30:00', NULL),
(8, 'images/image1.jpg', 'Passionate about sustainability? Join me in organizing a campus cleanup event! Lets make a positive impact on our environment. Comment if youre interested! 🌱 #SustainableLiving', '2023-08-05 19:30:00', NULL),
(8, NULL, 'Campus library = my second home. 📚 Wheres your go-to study spot?', '2023-08-04 19:30:00', NULL),
(8, NULL, 'Research paper deadline looming. 😬 Any procrastination hacks?', '2023-08-03 19:30:00', NULL),
(8, NULL, 'Attending a fascinating lecture on [topic] tomorrow. Whos in?', '2023-08-04 19:30:00', NULL),
(8, NULL, 'Just joined a campus club. 🤝 Excited to meet new faces!', '2023-08-03 19:30:00', NULL),
(8, NULL, 'Hump day feels. Whats getting you through the week?', '2023-08-05 19:30:00', NULL),
(8, NULL, 'Finished a chapter of my novel. Any bookworms here? 📖', '2023-08-02 19:30:00', NULL),
(8, NULL, 'Campus food review: hit or miss? Share your favorites and foodie adventures! 🍔🍕 Wondering if it''s a hit or miss? Whether it''s savory bites or sweet delights, every meal is an opportunity for culinary exploration. Share the hidden gems, the must-try dishes, and the cautionary tales about the not-so-appetizing options. Are you a fan of the campus pizza joint or the sushi spot around the corner? Burgers or salads? Let''s create a campus foodie guide filled with delicious recommendations!', '2023-08-03 19:30:00', NULL),
(8, NULL, 'Who else is counting down to the weekend? 🎉', '2023-07-30 19:30:00', NULL),
(8, NULL, 'Just aced that quiz! 🌟 Hows your day going, fellow students?0', '2023-07-27 19:30:00', NULL),
(9, 'images/image1.jpg', 'Just discovered a hidden gem of a concert venue near campus. Whos up for a night of live music? Drop your favorite genres, and lets plan a musical outing together! 🎶', '2023-07-25 19:30:00', NULL),
(9, NULL, 'Campus library = my second home. 📚 Wheres your go-to study spot?', '2023-07-23 19:30:00', NULL),
(9, NULL, 'Research paper deadline looming. 😬 Any procrastination hacks?', '2023-07-24 19:30:00', NULL),
(9, NULL, 'Attending a fascinating lecture on [topic] tomorrow. Whos in?', '2023-07-23 19:30:00', NULL),
(9, NULL, 'Just joined a campus club. 🤝 Excited to meet new faces!', '2023-07-22 19:30:00', NULL),
(9, NULL, 'Hump day feels. Whats getting you through the week?', '2023-07-21 19:30:00', NULL),
(9, NULL, 'Finished a chapter of my novel. Any bookworms here? 📖', '2023-07-23 19:30:00', NULL),
(9, NULL, 'Campus food review: hit or miss? Share your favorites!', '2023-07-22 19:30:00', NULL),
(9, NULL, 'Who else is counting down to the weekend? 🎉', '2023-07-20 19:30:00', NULL),
(9, NULL, 'Just aced that quiz! 🌟 Hows your day going, fellow students?0', '2023-07-21 19:30:00', NULL),
(10, 'image1.jpg', 'Dreaming about my next adventure during the break. Wheres your dream travel destination? Share your travel bucket list, and lets exchange stories and recommendations! 🌍✈️ #TravelGoals', '2023-07-19 19:30:00', NULL),
(10, NULL, 'Campus library = my second home. 📚 Wheres your go-to study spot?', '2023-07-17 19:30:00', NULL),
(10, NULL, 'Research paper deadline looming. 😬 Any procrastination hacks?', '2023-07-18 19:30:00', NULL),
(10, NULL, 'Exciting news! 🌟 Tomorrow, I''ll be attending a thought-provoking lecture on [topic] at our campus. The anticipation is building, and I can''t help but wonder: who else is in for an evening of intellectual exploration? Whether you''re a seasoned enthusiast in the subject or just curious to learn, join me for this fascinating journey of knowledge. Let''s dive deep into [topic], exchange insights, and perhaps grab a coffee afterward to discuss our takeaways. The more, the merrier! Who''s in for an enriching experience?', '2023-07-19 19:30:00', NULL),
(10, NULL, 'Just joined a campus club. 🤝 Excited to meet new faces!', '2023-07-15 19:30:00', NULL),
(10, NULL, 'Hump day feels. Whats getting you through the week?', '2023-07-14 19:30:00', NULL),
(10, NULL, 'Finished a chapter of my novel. Any bookworms here? 📖', '2023-07-13 19:30:00', NULL),
(10, NULL, 'Campus food review: hit or miss? Share your favorites!', '2023-07-11 19:30:00', NULL),
(10, NULL, 'Who else is counting down to the weekend? 🎉', '2023-07-10 19:30:00', NULL),
(10, NULL, 'Just aced that quiz! 🌟 Hows your day going, fellow students?0', '2023-07-06 19:30:00', NULL);

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
    (2, 1, 'accepted'),
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

-- -- Insert statements for the 'mentions' table
-- INSERT INTO mentions (post_id, user_mentioned)
-- VALUES
--     (1, 2),
--     (2, 1),
--     (3, 10),
--     (4, 3),
--     (5, 4),
--     (6, 1),
--     (7, 2),
--     (8, 9),
--     (9, 10),
--     (10, 1);

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
    (1, NULL, '2023-10-26', 'spam'),
    (2, NULL, '2023-10-25', 'inappropriate_content'),
    (NULL, 8, '2023-10-24', 'inappropriate_content'),
    (8, NULL, '2023-10-23', 'spam'),
    (8, NULL, '2023-10-22', 'inappropriate_content'),
    (NULL, 9, '2023-10-21', 'spam'),
    (9, NULL, '2023-10-20', 'inappropriate_content'),
    (9, NULL, '2023-10-19', 'spam'),
    (NULL, 1, '2023-10-18', 'inappropriate_content'),
    (NULL, 10, '2023-10-17', 'spam');


