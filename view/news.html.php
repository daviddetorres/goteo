<?php
use Goteo\Library\Text,
    Goteo\Model\Blog\Post;

$bodyClass = 'news';

$read_more = Text::get('regular-read_more');

// noticias
$news = $this['news'];

// últimas entradas del blog goteo
$list = array();

$title = Text::get('blog-side-last_posts');
$items = Post::getAll(1, 7);
// enlace a la entrada
foreach ($items as $item) {
    $list[] = '<a href="/blog/'.$item->id.'"> '.Text::recorta($item->title, 100).'</a>';
}

// paginacion
require_once 'library/pagination/pagination.php';

$pagedResults = new \Paginated($news, 7, isset($_GET['page']) ? $_GET['page'] : 1);

include 'view/prologue.html.php';
include 'view/header.html.php';
?>
<div id="sub-header-secondary">
    <div class="clearfix">
        <h2>GOTEO<span class="red">NEWS</span></h2>
        <ul class="share-goteo">
            <li class="twitter"><a href="#"><?php echo Text::get('regular-share-twitter'); ?></a></li>
            <li class="facebook"><a href="#"><?php echo Text::get('regular-share-facebook'); ?></a></li>
            <li class="rss"><a href="#"><?php echo Text::get('regular-share-rss'); ?></a></li>
        </ul>
    </div>
</div>
<div id="main" class="threecols">
    <div id="news-content">
        <?php while ($content = $pagedResults->fetchPagedRow()) : ?>
            <div class="widget news-content-module">
                <h3><?php echo $content->title; ?></h3>
                <blockquote><?php echo $content->description; ?></blockquote>
                <a href="<?php echo $content->url; ?>"><?php echo $read_more; ?></a>
            </div>
        <?php endwhile; ?>
        <ul class="pagination">
            <?php   $pagedResults->setLayout(new DoubleBarLayout());
                    echo $pagedResults->fetchPagedNavigation(); ?>
        </ul>
    </div>
    <div id="news-sidebar">
        <div class="widget news-sidebar-module">
            <h3 class="title"><?php echo $title; ?></h3>
            <ul id="news-side-posts">
                <?php foreach ($list as $item) : ?>
                <li><?php echo $item; ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
    </div>
</div>
<?php
include 'view/footer.html.php';
include 'view/epilogue.html.php';
?>