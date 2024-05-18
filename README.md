# Personal Homepage

Page: [yeftakun.my.id](http://yeftakun.my.id)

<a href="https://youtu.be/QtdbGIvLJKo?si=uFrV_p2AISCH4A52">Tutorial Deploy AWS</a>

Dynamic page: [blog.php](page\blog.php) & [blogcontent.php](page\blog-list\blogcontent.php)

### Database

```
Name DB: myblog

Tabel categories:
+--------------------------------------------------------------------+
| category_id | int(11)      | NO   | PRI | NULL    | auto_increment |
| name        | varchar(255) | NO   |     | NULL    |                |
+--------------------------------------------------------------------+

Tabel posts:
+-------------------------------------------------------------------------+
| post_id      | int(11)      | NO   | PRI | NULL        | auto_increment |
| title        | varchar(255) | NO   |     | NULL        |                |
| content      | text         | NO   |     | NULL        |                |
| author       | varchar(100) | NO   |     | NULL        |                |
| publish_date | date         | NO   |     | NULL        |                |
| image_path   | varchar(255) | NO   |     | default.png |                |
| source_link  | varchar(255) | YES  |     | NULL        |                |
| category_id  | int(11)      | YES  | MUL | NULL        |                |
+-------------------------------------------------------------------------+

Tabel visitor_count:
+---------------------------------------------------------+
| id    | int(11) | NO   | PRI | NULL    | auto_increment |
| COUNT | int(11) | YES  |     | 0       |                |
+---------------------------------------------------------+

Tabel password:
+----------------------------------------------------+
| id    | int(11)     | NO   | PRI | NULL    |       |
| pass  | varchar(12) | YES  |     | NULL    |       |
+----------------------------------------------------+
update password set pass='password_baru123';
```

### Add Blog

<p>Buat blog baru di <a href="ignorethis\add-blog.php">add-blog.php</a> (<a href="http://localhost/personal-homepage/ignorethis/add-blog.php">localhost/personal-homepage/ignorethis/add-blog.php</a>). Untuk bagian <strong>content</strong> buat dengan struktur html (header mulai h2)</p>

![content.png](ignorethis/content.png)

<p>Ukuran file tidak lebih dari 500KB</p>
