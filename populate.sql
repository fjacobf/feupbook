-- Insert statements for the 'users' table
INSERT INTO users (username, email, password, name, bio, private, user_type)
VALUES
    ('user1', 'user1@example.com', 'password1', 'User One', 'Bio for User One', false, 'normal_user'),
    ('user2', 'user2@example.com', 'password2', 'User Two', 'Bio for User Two', true, 'normal_user'),
    ('newuser', 'newuser@example.com', 'mypass', 'New User', 'Bio for New User', false, 'normal_user'),
    ('FPmod', 'fpmod@example.com', 'admin123', 'FPAdmin', 'Admin for FEUPbook', false, 'admin'),
    ('ADM', 'admfpbook@example.com', 'admin123', 'ADM', 'Second Admin for FEUPbook', true, 'admin'),
    ('FPhelper', 'helper@example.com', 'admin123', 'FPhelper', 'Helper for FEUPbook', false, 'admin'),
    ('FPhelper2', 'helper2@example.com', 'admin123', 'FPhelper2', 'Second helepr for FEUPbook', false, 'admin'),
    ('BadUser', 'baduser@example.com', 'banned123', 'BadUser', 'Banned from FEUPbook', false, 'suspended'),
    ('BadUser2', 'baduser2@example.com', 'banned123', 'BadUser2', 'Second banned from FEUPbook', false, 'suspended'),
    ( 'Joe', 'joe@example.com', 'password', 'Joe', 'Joe on feupbook', false, 'normal_user');

-- Insert statements for the 'post' table
INSERT INTO post (owner_id, image, content, date)
VALUES
    ( 1, 'image1.jpg', 'This is the content of the first post.', '2023-10-26'),
    ( 2, 'image2.jpg', 'Post by User Two.', '2023-10-25'),
    ( 4, NULL, 'Moderator post.', '2023-10-24'),
    ( 2, 'image4.jpg', 'Second post from User Two', '2023-10-23'),
    ( 7, NULL, 'Welcome to FEUPbook!', '2023-10-22'),
    ( 2, 'image6.jpg', 'Another post by User Two.', '2023-10-21'),
    ( 1, 'image7.jpg', 'Another post by User One.', '2023-10-20'),
    ( 8, 'image8.jpg', 'I got banned!', '2023-10-19'),
    ( 9, NULL, 'I also got banned.', '2023-10-18'),
    ( 10, NULL, 'Hello from Joe.', '2023-10-17');

-- Insert statements for the 'comment' table
INSERT INTO comment (author_id, post_id, content, date, previous)
VALUES
    (1, 1, 'Comment on post 1 by User One.', '2023-10-26', NULL),
    -- (2, 2, 1, 'Reply to comment 1 by User Two.', '2023-10-27', 1),  DOES NOT WORK DUE TO enforce_comment_privacy() TRIGGER
    (4, 2, 'Moderator comment on post 2.', '2023-10-28', NULL),
    -- (4, 2, 2, 'A moderator?', '2023-10-29', 3), DOES NOT WORK DUE TO enforce_comment_privacy() TRIGGER
    (6, 2, 'Another moderator.', '2023-10-30', NULL),
    -- (6, 2, 8, 'Deserved.', '2023-10-31', NULL), DOES NOT WORK DUE TO enforce_comment_privacy() TRIGGER
    (4, 8, 'I agree.', '2023-11-01', NULL),
    (3, 9, 'You too?', '2023-11-02', NULL),
    (3, 9, 'Why?', '2023-11-03', NULL),
    (10, 10, 'Hello from Joe.', '2023-11-04', NULL);


-- A INSERT INTO THE NOTIFICATION TABLE SHOULD NOT EXIST, ALL THE NOTIFICATIONS SHOULD COME FROM TRIGGERS
-- INSERT INTO notification (notification_id, date, notified_user, notification_type, comment_id, post_id, group_id, viewed)
-- VALUES
--     (1, '2023-10-26', 1, 'liked_comment', 1, NULL, NULL, false),
--     (2, '2023-10-27', 2, 'request_follow', NULL, NULL, NULL, true),
--     (3, '2023-10-28', 3, 'liked_post', NULL, 1, NULL, false),
--     (4, '2023-10-29', 4, 'commented_post', 2, 1, NULL, false),
--     (5, '2023-10-30', 5, 'mentioned', NULL, 2, NULL, false),
--     (6, '2023-10-31', 6, 'liked_comment', 4, NULL, NULL, false),
--     (7, '2023-11-01', 10, 'started_following', NULL, NULL, NULL, false),
--     (8, '2023-11-02', 8, 'liked_comment', 6, NULL, NULL, false),
--     (9, '2023-11-03', 9, 'liked_comment', 7, NULL, NULL, false),
--     (10, '2023-11-04', 10, 'liked_comment', 8, NULL, NULL, false);

-- Insert statements for the 'group_chat' table

INSERT INTO group_chat (owner_id, name, description)
VALUES
    ( 1, 'Group One', 'Description for Group One'),
    ( 2, 'Group Two', 'Description for Group Two'),
    ( 3, 'Group Three', 'Description for Group Three'),
    ( 4, 'Group Four', 'Description for Group Four'),
    ( 5, 'Group Five', 'Description for Group Five'),
    ( 6, 'Group Six', 'Description for Group Six'),
    ( 7, 'Group Seven', 'Description for Group Seven'),
    ( 8, 'Group Eight', 'Description for Group Eight'),
    ( 9, 'Group Nine', 'Description for Group Nine'),
    ( 10, 'Joe Group', 'For Joe lovers.');

-- Insert statements for the 'group_member' table
INSERT INTO group_member (user_id, group_id, status)
VALUES
    --(1, 1, 'accepted'),
    (2, 1, 'rejected'),
    (3, 2, 'accepted'),
    (4, 2, 'accepted'),
    (5, 3, 'accepted'),
    (6, 3, 'waiting'),
    (7, 4, 'rejected'),
    (8, 4, 'accepted'),
    (9, 10, 'accepted');
    --(10, 10, 'accepted');


-- Insert statements for the 'message' table
INSERT INTO message ( emitter_id, group_id, content, date, viewed)
VALUES
    ( 1, 1, 'Message in Group One by User One', '2023-10-26', false),
    ( 2, 2, 'Message in Group Two by User Two', '2023-10-25', true),
    --( 1, 2, 'Another message in Group Two by User One', '2023-10-24', true),
    --( 2, 1, 'Another message in Group One by User Two', '2023-10-23', true),
    ( 3, 3, 'Message in Group Three by User Three', '2023-10-22', true),
    ( 4, 4, 'Message in Group Four by User Four', '2023-10-21', true),
    ( 5, 5, 'Message in Group Five by User Five', '2023-10-20', true),
    ( 6, 6, 'Message in Group Six by User Six', '2023-10-19', true),
    ( 7, 7, 'Message in Group Seven by User Seven', '2023-10-18', true),
    ( 8, 8, 'Message in Group Eight by User Eight', '2023-10-17', true);

-- Insert statements for the 'follow_request' table
INSERT INTO follow_request (req_id, rcv_id, date, status)
VALUES
    (1, 2, '2023-10-26', 'waiting'),
    (2, 1, '2023-10-27', 'accepted'),
    (3, 2, '2023-10-28', 'waiting'),
    (4, 3, '2023-10-29', 'accepted'),
    (5, 4, '2023-10-30', 'rejected'),
    (6, 5, '2023-10-31', 'waiting'),
    (7, 6, '2023-11-01', 'waiting'),
    (8, 7, '2023-11-02', 'accepted'),
    (9, 8, '2023-11-03', 'accepted'),
    (10, 9, '2023-11-04', 'accepted');

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
    --(2, 2),
    (3, 3),
    --(4, 4),
    (5, 5),
    --(6, 6),
    (7, 7),
    (8, 8),
    (9, 9),
    (10, 10);

-- Insert statements for the 'mention' table
INSERT INTO mention (post_id, user_mentioned)
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

-- Insert statements for the 'report' table
INSERT INTO report (user_id, post_id, date, report_type)
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


