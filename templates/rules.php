<body>
    <div class='home'>
        <div class='header'>
            <h1><a href='../'><</a> Rules</h1>
        </div>
        <div class='description'>
            By using this site you agree to and will abide by the rules laid out below. 'global' rules must be followed on every board. Every board has its own specific rules that will need to be followed too.
            This page and its content also equate to a Terms Of Service agreement.
        </div>
        <div class='content'>
            <div class='linkcontainer'>
                <?php foreach($this->rules as $rule): ?>

                    <div class='link'>
                        <a href='?b=<?=$rule->getBoard()?>'><?=$rule->getBoard()?></a> - <?=$rule->getTitle()?>
                        <br><i><?=$rule->getContent()?></i>
                    </div>
                        <?php endforeach ?>
            </div>
        </div>
    </div>