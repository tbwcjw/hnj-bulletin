<body>
    <div class='board'>
        <div class='header'> 
            <h1><a href='../'><</a> <?php echo $this->boards->getTitle() ?></h1>
        </div>
        <div class='description'>
            <?php echo $this->boards->getDescription() ?>
            <br><br>
            <form style='float:right;' method='get'>
                <input type='hidden' name='b' value='<?=$this->boards->getLink() ?>'>
                <?php if(!in_array($this->boards->getLink(), READONLY_BOARDS)): ?>
                    <a href='?i=n'>new post</a>
                <?php endif ?>
                | search: <input type='text' name='q' required>
                <input type='submit' value='Search'><a href='?i=r'> rules</a> | <a href='?i=h'>support</a> | <a href='?i=s'>&#9881;</a>
            </form>
            <br>
        </div>
        <div class='content'>
            <div class='postcontainer'>
                <?php foreach($this->posts as $post): ?>
                    <div class='post'>
                        <div class='header'>
                            <a href='?b=<?=$this->boards->getLink() ?>&p=<?=$post->getID() ?>'><?=$post->getID()?></a> - <?=$post->getPoster()?>
                        </div>
                        <div class='content'>
                            <p>Subject: <i><?=$post->getSubject()?></i></p>
                            <p><?=truncate($post->getContent())?></p>
                        </div>
                    </div>
                    <?php endforeach ?>
            </div>
        </div>
    </div>