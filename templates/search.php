<body>
    <div class='thread'>
        <div class='header'> 
        <h1><a href='../'><</a> Search Results</h1>

        <div class='description'>
            <p>Showing results for <i>"<?php echo htmlspecialchars(filter_var($_GET['q'], FILTER_DEFAULT), ENT_QUOTES, 'UTF-8'); ?>"</i>
            <form style='float:right;' method='get'>
                search: <input type='text' name='q' required>
                <input type='submit' value='Search'><a href='FUCK'><a href='?i=r'> rules</a> | <a href='?i=h'>support</a> | <a href='?i=s'>&#9881;</a>
            </form>
            <br>
        </div>        
    </div>
        <div class='content'>
        <?php foreach($this->results as $result): ?>
            <div class='threadcontainer'>
            
                <div class='post'>
                    <div class='header'>
                        <a href='?b=<?=$result->getBoard() ?>'><?=$result->getBoard(); ?></a> | <a href="?b=<?=$result->getBoard(); ?>&p=<?=$result->getID() ?>"><?=$result->getID()?></a> <i>Posted by: </i> <?=$result->getPoster()?> <i>at </i> <?=$result->getDatetime() ?> | <a href=''>report</a> | <i>Confidence: <?=$result->getConfidence()?></i>
                    </div>
                    <div class='content'>
                        <div class='subject'>
                            Subject: <i><?=$result->getSubject()?></i>
                        </div>
                        <div class='threadcontent'>
                            <pre><?=$result->getContent()?></pre>
                        </div>
                    </div>
                </div>
                
            </div>
            <?php endforeach ?>
        </div>
    </div>