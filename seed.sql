create schema if not exists lbaw2255;

SET DateStyle TO European;

-----------------------------------------
-- Drop old schema
-----------------------------------------

-- Drop Table Statements
DROP TABLE IF EXISTS report;
DROP TABLE IF EXISTS bookmarks;
DROP TABLE IF EXISTS suspended;
DROP TABLE IF EXISTS admin;
DROP TABLE IF EXISTS mention;
DROP TABLE IF EXISTS comment_likes;
DROP TABLE IF EXISTS post_likes;
DROP TABLE IF EXISTS group_member;
DROP TABLE IF EXISTS group_chat;
DROP TABLE IF EXISTS follow_request;
DROP TABLE IF EXISTS message;
DROP TABLE IF EXISTS group_notification;
DROP TABLE IF EXISTS post_notification;
DROP TABLE IF EXISTS user_notification;
DROP TABLE IF EXISTS comment_notification;
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
    notified_user INT REFERENCES users(userId) NOT NULL,
    emitter_user INT REFERENCES users(userId) NOT NULL,
    viewed BOOLEAN DEFAULT false
);

-- R11: group_chat
CREATE TABLE group_chat (
    group_id INT PRIMARY KEY,
    owner_id INT REFERENCES users(userId) NOT NULL,
    name VARCHAR(255) NOT NULL,
    description TEXT
);


-- R05: comment_notification
CREATE TABLE comment_notification (
    id INT PRIMARY KEY REFERENCES notification(id),
    comment_id INT REFERENCES comment(id),
    notification_type VARCHAR(255) NOT NULL CHECK (notification_type IN ('comment_notification_types'))
);

-- R06: user_notification
CREATE TABLE user_notification (
    id INT PRIMARY KEY REFERENCES notification(id),
    user_id INT REFERENCES users(userId),
    notification_type VARCHAR(255) NOT NULL CHECK (notification_type IN ('user_notification_types'))
);

-- R07: post_notification
CREATE TABLE post_notification (
    id INT PRIMARY KEY REFERENCES notification(id),
    post_id INT REFERENCES post(postId) NOT NULL,
    notification_type VARCHAR(255) NOT NULL CHECK (notification_type IN ('post_notification_types'))
);

-- R08: group_notification
CREATE TABLE group_notification (
    id INT PRIMARY KEY REFERENCES notification(id),
    group_id INT REFERENCES group_chat(group_id) NOT NULL,
    notification_type VARCHAR(255) NOT NULL CHECK (notification_type IN ('group_notification_types'))
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

-- R16: admin
CREATE TABLE admin (
    id INT PRIMARY KEY REFERENCES users(userId)
);

-- R17: suspended
CREATE TABLE suspended (
    id INT PRIMARY KEY REFERENCES users(userId)
);

-- R18: bookmarks
CREATE TABLE bookmarks (
    bookmarked_post INT REFERENCES post(postId),
    user_id INT REFERENCES users(userId),
    PRIMARY KEY (bookmarked_post, user_id)
);

-- R19: report
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

------------------------------
-- FTS INDEXES
------------------------------

------------------------------
-- TRIGGERS
------------------------------

------------------------------
-- TRANSACTIONS
------------------------------