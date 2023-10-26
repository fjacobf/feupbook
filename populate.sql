-- Insert statements for the 'users' table
INSERT INTO users (userId, username, email, password, name, bio, private)
VALUES
    (1, 'user1', 'user1@example.com', 'password1', 'User One', 'Bio for User One', false),
    (2, 'user2', 'user2@example.com', 'password2', 'User Two', 'Bio for User Two', true);

-- Insert statements for the 'post' table
INSERT INTO post (postId, owner_id, image, content, date)
VALUES
    (1, 1, 'image1.jpg', 'This is the content of the first post.', '2023-10-26'),
    (2, 2, 'image2.jpg', 'Another post by User Two.', '2023-10-25');

-- Insert statements for the 'comment' table
INSERT INTO comment (id, author_id, post_id, content, date, previous)
VALUES
    (1, 1, 1, 'Comment on post 1 by User One.', '2023-10-26', NULL),
    (2, 2, 1, 'Reply to comment 1 by User Two.', '2023-10-27', 1);

-- Insert statements for the 'notification' table
INSERT INTO notification (id, date, notified_user, notification_type, comment_id, post_id, group_id, viewed)
VALUES
    (1, '2023-10-26', 1, 'liked_comment', 1, NULL, NULL, false),
    (2, '2023-10-27', 2, 'request_follow', NULL, NULL, NULL, true);

-- Insert statements for the 'group_chat' table
INSERT INTO group_chat (group_id, owner_id, name, description)
VALUES
    (1, 1, 'Group One', 'Description for Group One'),
    (2, 2, 'Group Two', 'Description for Group Two');

-- Insert statements for the 'message' table
INSERT INTO message (id, emitter_id, group_id, content, date, viewed)
VALUES
    (1, 1, 1, 'Message in Group One by User One', '2023-10-26', false),
    (2, 2, 2, 'Message in Group Two by User Two', '2023-10-25', true);

-- Insert statements for the 'follow_request' table
INSERT INTO follow_request (req_id, rcv_id, date, status)
VALUES
    (1, 2, '2023-10-26', 'pending'),
    (2, 1, '2023-10-27', 'accepted');

-- Insert statements for the 'group_member' table
INSERT INTO group_member (user_id, group_id)
VALUES
    (1, 1),
    (2, 1);

-- Insert statements for the 'post_likes' table
INSERT INTO post_likes (user_id, post_id)
VALUES
    (1, 1),
    (2, 1);

-- Insert statements for the 'comment_likes' table
INSERT INTO comment_likes (user_id, comment_id)
VALUES
    (1, 1),
    (2, 2);

-- Insert statements for the 'mention' table
INSERT INTO mention (post_id, user_mentioned)
VALUES
    (1, 2),
    (2, 1);

-- Insert statements for the 'bookmarks' table
INSERT INTO bookmarks (bookmarked_post, user_id)
VALUES
    (1, 1),
    (2, 2);

-- Insert statements for the 'report' table
INSERT INTO report (id, user_id, post_id, date, report_type)
VALUES
    (1, 1, 2, '2023-10-26', 'spam'),
    (2, 2, 1, '2023-10-25', 'inappropriate');
