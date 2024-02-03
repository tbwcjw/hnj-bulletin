<body>
    <div class='thread'>
        <div class='header'> 
        <h1><a href='?b=<?php echo $this->boards->getLink() ?>'><</a> <?php echo $this->boards->getTitle() ?></h1>
        </div>
        <div class='description'>
            <?php echo $this->boards->getDescription() ?>
            <br><br>
            <form style='float:right;' method='get'>
                <input type='hidden' name='b' value='<?=$this->boards->getLink() ?>'>
                <?php if(!in_array($this->boards->getLink(), READONLY_BOARDS)): ?>
                    <a href=''>new post</a>
                <?php endif ?>
                | search: <input type='text' name='q' required>
                <input type='submit' value='Search'><a href='?i=r'> rules</a> | <a href='?i=h'>support</a> | <a href='?i=s'>&#9881;</a>
            </form>
            <br>
        </div>
        <div class='content'>
            <div class='threadcontainer'>
                <div class='post'>
                    <div class='header'>
                        <a href='?b=<?=$this->boards->getLink() ?>&p=<?=$this->posts->getID() ?>'><?=$this->posts->getID()?></a> - <i>Posted by: </i> <?=$this->posts->getPoster()?> <i>at </i> <?= date((string)$this->dt_format, strtotime($this->posts->getDatetime())) ?> | <a href='<?=$this->posts->getMagnet() ?>'>magnet link</a> | <a href=''>report</a>
                    </div>
                    <div class='content'>
                        <div class='subject'>
                            Subject: <i><?=$this->posts->getSubject()?>
                        </div>
                        <div class='threadcontent'>
                            <pre><?=$this->posts->getContent()?></pre>
                        </div>
                    </div>
                </div>
            </div>
            <?php if(!in_array($this->boards->getLink(), READONLY_BOARDS)): ?>
            <div class='reply'>
                <div class='header'>Add a reply</div> 
                <div class='replycontent'>
                    <form action='upload.php?n=r&b=<?=$this->boards->getLink()?>&p=<?=$this->posts->getID()?>' method='POST' accept-charset="UTF-8">
                        <input type='hidden' name='captcha_answer' value='<?=$_SESSION['captcha_answer'] ?>'>
                        <input type='hidden' name='csrf_token' value='<?=$_SESSION['csrf_token'] ?>'>
                        <input type='hidden' name='bot' value=''>
                        <table>
                            <tr>
                                <td class='title'>Alias</td>
                                <td class='felem'><input type='text' name='username' placeholder="Anonymous" required></td>
                                <td class='tip'>Defaults to 'Anonymous'</td>
                            </tr>
                            <tr>
                                <td class='title'>Content</td>
                                <td class='felem'><textarea name='content' required>A bee bit my bottom, now my bottom is big!</textarea></td>
                                <td class='tip'>You must comply to the rules and formatting of this board.</td>
                            </tr>
                            <tr>
                                <td class='title'><img src ='<?=$this->captcha ?>'></td>
                                <td class='felem'><input type='number' name='captcha' required></td>
                                <td class='tip'>Complete the question</td>
                            </tr>
                            <tr>
                                <td class='title'></td>
                                <td class='felem'><input type='checkbox' name='agreement' required>I agree to the terms of use of this site, the <a href='?i=r'>global</a> and <a href='?i=r'>board specific</a> rules</td>
                                <td class='tip'><input type='submit'></td>
                            </tr>
                        </table>
                    </form>
                </div>
            </div>
            
            <?php foreach($this->replies as $reply): ?>
                <div class='reply'>
                    <div class='header'><i>Posted by:</i> <?=$reply->getPoster() ?> <i>at</i> <?=$reply->getDatetime()?> | <a href=''>report</a></div>
                    <div class='replycontent'><pre><?=$reply->getContent() ?></pre></div>
                </div>
            <?php endforeach ?>
            <?php endif ?>
        </div>
    </div>