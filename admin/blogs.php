<?php
ob_start();
require_once __DIR__ . '/includes/header.php';

$success_message = '';
$error_message = '';

if (isset($_GET['success'])) {
    if ($_GET['success'] === 'add') $success_message = 'Blog post published successfully!';
    if ($_GET['success'] === 'edit') $success_message = 'Blog post updated successfully!';
    if ($_GET['success'] === 'delete') $success_message = 'Blog post deleted successfully.';
}
if (isset($_GET['error'])) {
    if ($_GET['error'] === 'csrf') $error_message = 'Security check failed. Please refresh and try again.';
    else if ($_GET['error'] === 'add') $error_message = 'Failed to publish blog post (slug URL might be already taken).';
    else if ($_GET['error'] === 'edit') $error_message = 'Failed to update blog post details (slug URL might be already taken).';
    else if ($_GET['error'] === 'delete') $error_message = 'Failed to delete blog post.';
    else if ($_GET['error'] === 'req') $error_message = 'Title, category, and article content are required fields.';
}

// Handle Blog CRUD Submissions
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    $csrf_token = isset($_POST['csrf_token']) ? trim($_POST['csrf_token']) : '';

    if (!verify_csrf_token($csrf_token)) {
        header("Location: blogs.php?error=csrf");
        exit;
    } else {
        $action = $_POST['action'];

        if ($action === 'add' || $action === 'edit') {
            $title = isset($_POST['title']) ? htmlspecialchars(trim($_POST['title'])) : '';
            $category = isset($_POST['category']) ? htmlspecialchars(trim($_POST['category'])) : '';
            $content = isset($_POST['content']) ? trim($_POST['content']) : '';
            
            // SEO Inputs
            $slug = isset($_POST['slug']) ? trim($_POST['slug']) : '';
            $meta_title = isset($_POST['meta_title']) ? htmlspecialchars(trim($_POST['meta_title'])) : '';
            $meta_description = isset($_POST['meta_description']) ? htmlspecialchars(trim($_POST['meta_description'])) : '';

            // Auto-Generate Slug if empty
            if (empty($slug)) {
                $slug = strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $title)));
            } else {
                $slug = strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $slug)));
            }

            // Excerpt (Optional)
            $excerpt = isset($_POST['excerpt']) ? htmlspecialchars(trim($_POST['excerpt'])) : '';
            if (empty($excerpt)) {
                // Strip tags, collapse spaces, slice first 150 chars
                $plain_text = trim(preg_replace('/\s+/', ' ', strip_tags($content)));
                $excerpt = mb_substr($plain_text, 0, 150);
                if (mb_strlen($plain_text) > 150) {
                    $excerpt .= '...';
                }
                $excerpt = htmlspecialchars($excerpt);
            }

            // Auto-Generate Date
            $date = date('d M Y');

            // Auto-Generate Read Time (Word count estimation, 200 wpm average)
            $word_count = str_word_count(strip_tags($content));
            $read_minutes = ceil($word_count / 200);
            if ($read_minutes < 1) $read_minutes = 1;
            $read_time = $read_minutes . ' min read';

            // Auto Fallbacks for SEO Meta values
            if (empty($meta_title)) {
                $meta_title = $title;
            }
            if (empty($meta_description)) {
                $meta_description = $excerpt;
            }

            if (empty($title) || empty($category) || empty($content)) {
                header("Location: blogs.php?error=req");
                exit;
            } else {
                // Image File Handling
                $image_path = 'assets/imgs/page/homepage1/news.png'; // default fallback
                if ($action === 'edit' && isset($_POST['existing_image_path'])) {
                    $image_path = $_POST['existing_image_path'];
                }

                $image_uploaded = false;
                if (isset($_FILES['blog_image']) && $_FILES['blog_image']['error'] === UPLOAD_ERR_OK) {
                    $file_tmp = $_FILES['blog_image']['tmp_name'];
                    $file_name = $_FILES['blog_image']['name'];
                    $file_ext = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));

                    $allowed_extensions = ['jpg', 'jpeg', 'png', 'gif', 'webp'];

                    if (in_array($file_ext, $allowed_extensions)) {
                        $upload_dir = __DIR__ . '/../uploads/';
                        if (!is_dir($upload_dir)) {
                            mkdir($upload_dir, 0755, true);
                        }

                        $new_filename = uniqid('blog_', true) . '.' . $file_ext;
                        $dest_path = $upload_dir . $new_filename;

                        if (move_uploaded_file($file_tmp, $dest_path)) {
                            // If edit action, clean up the old file
                            if ($action === 'edit' && strpos($image_path, 'uploads/') === 0 && file_exists(__DIR__ . '/../' . $image_path)) {
                                unlink(__DIR__ . '/../' . $image_path);
                            }
                            $image_path = 'uploads/' . $new_filename;
                            $image_uploaded = true;
                        }
                    }
                }

                if ($action === 'add') {
                    try {
                        $stmt = $pdo->prepare("INSERT INTO blogs (slug, title, category, image_path, date, read_time, excerpt, content, meta_title, meta_description) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
                        $stmt->execute([$slug, $title, $category, $image_path, $date, $read_time, $excerpt, $content, $meta_title, $meta_description]);
                        header("Location: blogs.php?success=add");
                        exit;
                    } catch (Exception $e) {
                        error_log("Blog insertion failure: " . $e->getMessage());
                        // Clean up newly uploaded image if insert failed
                        if ($image_uploaded && file_exists(__DIR__ . '/../' . $image_path)) {
                            unlink(__DIR__ . '/../' . $image_path);
                        }
                        header("Location: blogs.php?error=add");
                        exit;
                    }
                } else if ($action === 'edit') {
                    $id = isset($_POST['id']) ? intval($_POST['id']) : 0;
                    try {
                        $stmt = $pdo->prepare("UPDATE blogs SET slug = ?, title = ?, category = ?, image_path = ?, date = ?, read_time = ?, excerpt = ?, content = ?, meta_title = ?, meta_description = ? WHERE id = ?");
                        $stmt->execute([$slug, $title, $category, $image_path, $date, $read_time, $excerpt, $content, $meta_title, $meta_description, $id]);
                        header("Location: blogs.php?success=edit");
                        exit;
                    } catch (Exception $e) {
                        error_log("Blog edit failure: " . $e->getMessage());
                        header("Location: blogs.php?error=edit");
                        exit;
                    }
                }
            }
        } else if ($action === 'delete') {
            $id = isset($_POST['id']) ? intval($_POST['id']) : 0;
            try {
                // Fetch image details to clean up disk file
                $stmt = $pdo->prepare("SELECT image_path FROM blogs WHERE id = ?");
                $stmt->execute([$id]);
                $item = $stmt->fetch();

                if ($item) {
                    $relative_path = $item['image_path'];
                    $full_filepath = __DIR__ . '/../' . $relative_path;

                    $stmt_del = $pdo->prepare("DELETE FROM blogs WHERE id = ?");
                    $stmt_del->execute([$id]);

                    if (strpos($relative_path, 'uploads/') === 0 && file_exists($full_filepath)) {
                        unlink($full_filepath);
                    }

                    header("Location: blogs.php?success=delete");
                    exit;
                } else {
                    header("Location: blogs.php?error=delete");
                    exit;
                }
            } catch (Exception $e) {
                error_log("Blog deletion failure: " . $e->getMessage());
                header("Location: blogs.php?error=delete");
                exit;
            }
        }
    }
}

// Fetch current blogs from DB
$blogs = [];
try {
    $blogs = $pdo->query("SELECT * FROM blogs ORDER BY id DESC")->fetchAll();
} catch (Exception $e) {
    error_log("Blogs loading error in admin page: " . $e->getMessage());
}
?>

<!-- Include TinyMCE from CDN (Immune to theme font overrides, uses vector SVGs) -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/tinymce/6.8.2/tinymce.min.js"></script>

<div class="d-flex justify-content-between align-items-center mb-35">
    <div>
        <h1 class="panel-title mb-0" style="font-size:26px;">Manage Blog Posts</h1>
        <p class="text-sm text-neutral-500 mt-5">Publish, edit, or remove articles and attractions guides on your website.</p>
    </div>
    <button class="btn btn-black text-white" onclick="showAddForm()" style="padding: 10px 24px; border-radius: 8px; font-size:14px;">
        Add New Article
    </button>
</div>

<?php if (!empty($success_message)): ?>
    <div class="alert alert-success alert-dismissible fade show mb-25" style="border-radius: 8px; font-size:14px; padding: 12px 20px;">
        <?= htmlspecialchars($success_message) ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
<?php endif; ?>

<?php if (!empty($error_message)): ?>
    <div class="alert alert-danger alert-dismissible fade show mb-25" style="border-radius: 8px; font-size:14px; padding: 12px 20px;">
        <?= htmlspecialchars($error_message) ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
<?php endif; ?>

<!-- Blog Editor Form Card (Initially Hidden) -->
<div id="blogFormCard" class="panel-card" style="display:none;">
    <h3 id="formTitle" class="font-heading mb-25" style="font-size:18px;">Publish New Article</h3>
    
    <form id="blogEditor" action="blogs.php" method="POST" enctype="multipart/form-data" onsubmit="tinymce.triggerSave();">
        <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">
        <input type="hidden" id="formAction" name="action" value="add">
        <input type="hidden" id="blogIdInput" name="id" value="">
        <input type="hidden" id="existingImagePath" name="existing_image_path" value="">

        <div class="row g-3">
            <div class="col-md-6">
                <div class="form-group">
                    <label class="form-label-custom">Article Title *</label>
                    <input id="blogTitle" class="form-control-custom" type="text" name="title" placeholder="e.g. Gwalior Fort Guide..." oninput="autoGenerateSlug(this.value)" required>
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group">
                    <label class="form-label-custom">Category *</label>
                    <input id="blogCategory" class="form-control-custom" type="text" name="category" placeholder="e.g. Local Attractions, Dining Guide" required>
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group">
                    <label class="form-label-custom">Featured Image File</label>
                    <input class="form-control-custom" type="file" name="blog_image" accept="image/*" style="padding-top:12px;">
                    <span style="font-size:11.5px; color:#888; display:block; margin-top:5px;">Upload PNG, JPG, or WEBP. Leave blank to keep existing.</span>
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group">
                    <label class="form-label-custom">URL Slug * (Auto-Generated)</label>
                    <input id="blogSlug" class="form-control-custom" type="text" name="slug" placeholder="gwalior-fort-guide" required>
                </div>
            </div>

            <!-- SEO Parameters section -->
            <div class="col-md-6">
                <div class="form-group">
                    <label class="form-label-custom">SEO Meta Title (Fallback to Title if blank)</label>
                    <input id="blogMetaTitle" class="form-control-custom" type="text" name="meta_title" placeholder="Enter meta title for search engines...">
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group">
                    <label class="form-label-custom">SEO Meta Description (Fallback to Excerpt if blank)</label>
                    <input id="blogMetaDesc" class="form-control-custom" type="text" name="meta_description" placeholder="Enter meta description snippets...">
                </div>
            </div>

            <div class="col-md-12">
                <div class="form-group">
                    <label class="form-label-custom">Short Excerpt (Grid Card Preview - Optional)</label>
                    <textarea id="blogExcerpt" class="form-control-custom" name="excerpt" rows="2" placeholder="Brief summary of the article. Left blank to auto-generate from content..."></textarea>
                </div>
            </div>
            
            <div class="col-md-12">
                <div class="form-group">
                    <label class="form-label-custom">Detailed Article Content *</label>
                    <textarea id="blogContent" class="form-control-custom" name="content" placeholder="Write full article body details..."></textarea>
                </div>
            </div>
            
            <div class="col-md-12 mt-25 d-flex gap-10">
                <button class="btn btn-black text-white" type="submit" style="padding: 12px 30px; border-radius: 8px; font-weight:700;">Publish Article</button>
                <button class="btn btn-outline-dark" type="button" onclick="cancelEditor()" style="padding: 12px 30px; border-radius: 8px; border-color:#ccc;">Cancel</button>
            </div>
        </div>
    </form>
</div>

<!-- Blog Articles Table List -->
<div class="panel-card mt-30">
    <h3 class="font-heading" style="font-size:18px;">Published Articles</h3>
    
    <div class="table-responsive">
        <table class="table-custom">
            <thead>
                <tr>
                    <th>Article Preview</th>
                    <th>Category</th>
                    <th>Date Published</th>
                    <th>Read Time</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if (count($blogs) > 0): ?>
                    <?php foreach ($blogs as $b): ?>
                        <tr>
                            <td>
                                <div class="d-flex align-items-center" style="gap: 20px;">
                                    <div style="width:70px; height:50px; border-radius:6px; overflow:hidden; background:#eee; flex-shrink: 0;">
                                        <img src="../<?= htmlspecialchars($b['image_path']) ?>" alt="Preview" style="width:100%; height:100%; object-fit:cover;">
                                    </div>
                                    <div>
                                        <strong><?= htmlspecialchars($b['title']) ?></strong><br>
                                        <span style="font-size:12px; color:#666; max-width: 300px; display: block; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;"><?= htmlspecialchars($b['excerpt']) ?></span>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <span class="badge bg-light text-dark border" style="font-size:11px; padding:4px 8px;">
                                    <?= htmlspecialchars($b['category']) ?>
                                </span>
                            </td>
                            <td><?= htmlspecialchars($b['date']) ?></td>
                            <td><?= htmlspecialchars($b['read_time']) ?></td>
                            <td>
                                <div class="d-flex align-items-center" style="gap: 12px;">
                                    <button class="btn-edit" onclick="editBlog(<?= htmlspecialchars(json_encode($b)) ?>)">
                                        <svg width="12" height="12" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg" style="display:inline-block; vertical-align:middle; margin-right:4px; margin-top:-2px;"><path stroke-linecap="round" stroke-linejoin="round" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"></path></svg>Edit
                                    </button>
                                    
                                    <form action="blogs.php" method="POST" onsubmit="return confirm('Are you sure you want to delete this article?')" style="display:inline; margin:0;">
                                        <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">
                                        <input type="hidden" name="action" value="delete">
                                        <input type="hidden" name="id" value="<?= $b['id'] ?>">
                                        <button class="btn-delete" type="submit">
                                            <svg width="12" height="12" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg" style="display:inline-block; vertical-align:middle; margin-right:4px; margin-top:-2px;"><path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>Delete
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="5" class="text-center py-30 text-neutral-500">No blog posts configured. Create one using the button above.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<script>
    function autoGenerateSlug(title) {
        // Simple client-side slug generator
        const slug = title.toLowerCase()
            .replace(/[^a-z0-9\s-]/g, '') // remove special characters
            .replace(/\s+/g, '-')         // replace spaces with dashes
            .replace(/-+/g, '-');         // remove duplicate dashes
        document.getElementById('blogSlug').value = slug;
    }

    function showAddForm() {
        document.getElementById('formTitle').innerText = 'Publish New Article';
        document.getElementById('formAction').value = 'add';
        document.getElementById('blogIdInput').value = '';
        document.getElementById('existingImagePath').value = '';
        
        document.getElementById('blogTitle').value = '';
        document.getElementById('blogCategory').value = '';
        document.getElementById('blogSlug').value = '';
        document.getElementById('blogMetaTitle').value = '';
        document.getElementById('blogMetaDesc').value = '';
        document.getElementById('blogExcerpt').value = '';
        
        // Clear TinyMCE content
        if (tinymce.get('blogContent')) {
            tinymce.get('blogContent').setContent('');
        }

        document.getElementById('blogFormCard').style.display = 'block';
        window.scrollTo({ top: document.getElementById('blogFormCard').offsetTop - 30, behavior: 'smooth' });
    }

    function editBlog(blog) {
        document.getElementById('formTitle').innerText = 'Edit Published Article';
        document.getElementById('formAction').value = 'edit';
        document.getElementById('blogIdInput').value = blog.id;
        document.getElementById('existingImagePath').value = blog.image_path;

        document.getElementById('blogTitle').value = blog.title;
        document.getElementById('blogCategory').value = blog.category;
        document.getElementById('blogSlug').value = blog.slug;
        document.getElementById('blogMetaTitle').value = blog.meta_title || '';
        document.getElementById('blogMetaDesc').value = blog.meta_description || '';
        document.getElementById('blogExcerpt').value = blog.excerpt;
        
        // Load content into TinyMCE
        if (tinymce.get('blogContent')) {
            tinymce.get('blogContent').setContent(blog.content || '');
        }

        document.getElementById('blogFormCard').style.display = 'block';
        window.scrollTo({ top: document.getElementById('blogFormCard').offsetTop - 30, behavior: 'smooth' });
    }

    function cancelEditor() {
        document.getElementById('blogFormCard').style.display = 'none';
    }
</script>

<?php require_once __DIR__ . '/includes/footer.php'; ?>

<!-- Initialize TinyMCE Editor with full toolbars (uses vector SVGs) -->
<script>
    $(document).ready(function() {
        tinymce.init({
            selector: '#blogContent',
            height: 380,
            plugins: 'advlist autolink lists link image charmap preview anchor searchreplace visualblocks code fullscreen insertdatetime media table wordcount',
            toolbar: 'undo redo | blocks | fontfamily fontsize | bold italic underline strikethrough | forecolor backcolor | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | link image table | code fullscreen',
            branding: false,
            promotion: false,
            setup: function(editor) {
                editor.on('change', function() {
                    editor.save();
                });
            }
        });
    });
</script>
