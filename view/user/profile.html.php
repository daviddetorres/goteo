<?php

use Goteo\Core\View,
    Goteo\Library\Worth,
    Goteo\Library\Text,
    Goteo\Model\User\Interest;

$bodyClass = 'user-profile';
include 'view/prologue.html.php';
include 'view/header.html.php';

$user = $this['user'];
$worthcracy = Worth::getAll();
?>

        <div id="sub-header">
            <div>                                
                <h2><img src="/image/<?php echo $user->avatar->id; ?>/75/75" /> <?php echo Text::get('profile-name-header'); ?> <br /><em><?php echo $user->name; ?></em></h2>
            </div>
        </div>

        <div id="main">
            
            <div class="center">
            
                <?php echo new View('view/user/widget/worth.html.php', array('worthcracy' => $worthcracy, 'level' => $user->worth)) ?>

                <?php echo new View('view/user/widget/about.html.php', array('user' => $user)) ?>

                <?php echo new View('view/user/widget/social.html.php', array('user' => $user)) ?>                        
                                                
                <div class="widget projects">
                    <h2 class="title"><?php echo Text::get('profile-invest_on-header'); ?></h2>
                    <?php foreach ($this['invested'] as $project) : ?>
                        <div>
                            <?php
                            // es instancia del proyecto
                            echo new View('view/project/widget/project.html.php', array(
                                'project'   => $project
                            )); ?>
                        </div>
                    <?php endforeach; ?>
                </div>

                <div class="widget projects">
                    <h2 class="title"><?php echo Text::get('profile-my_projects-header'); ?></h2>
                    <?php foreach ($this['projects'] as $project) : ?>
                        <div>
                            <?php
                            // es instancia del proyecto
                            echo new View('view/project/widget/project.html.php', array(
                                'project'   => $project
                            )); ?>
                        </div>
                    <?php endforeach; ?>
                    </div>

            </div>
            <div class="side">
                <div class="widget user-supporters">
                        <h3 class="supertitle"><?php echo Text::get('profile-my_investors-header'); ?></h3>
                        <div class="supporters">
                        <ul>
                            <?php foreach ($this['investors'] as $user => $investor): ?>
                            <li><?php echo new View('view/user/widget/supporter.html.php', array('user' => $investor, 'worthcracy' => $worthcracy)) ?></li>                        
                            <?php endforeach ?>
                        </ul>
                        </div>                        
                    </div>

                    <div class="widget user-mates">
                        <h3 class="supertitle"><?php echo Text::get('profile-sharing_interests-header'); ?></h3>
                        <div class="users">
                            <ul>
                            <?php foreach ($this['shares'] as $mate): ?>
                                <li>
                                    <div class="user">
                                        <?php if ($mate->avatar instanceof Goteo\Model\Image): ?>
                                        <div class="avatar"><img src="<?php echo htmlspecialchars($mate->avatar->getLink(50,50)) ?>" /></div>
                                        <?php endif ?>
                                        <h4><a href="/user/<?php echo htmlspecialchars($mate->user) ?>"><?php echo htmlspecialchars($mate->user) ?></a></h4>
                                        <a class="projects" href="/user/<?php echo htmlspecialchars($mate->user) ?>"><?php echo Text::get('regular-projects'); ?> (<?php echo $mate->projects ?>)</a>
                                        <a class="invests" href="/user/<?php echo htmlspecialchars($mate->user) ?>"><?php echo Text::get('regular-investing'); ?> (<?php echo $mate->invests ?>)</a>
                                    </div>
                                </li>
                            <?php endforeach ?>                                                        
                            </ul>
                        </div>
                        <a class="more" href=""><?php echo Text::get('regular-see_more'); ?></a>
                    </div>
            </div>

        </div>
        
    <?php include 'view/footer.html.php' ?>

<?php include 'view/epilogue.html.php' ?>