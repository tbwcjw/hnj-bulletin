<body>
    <div class='home'>
        <div class='header'>
            <h1><a href='../'>< </a>Settings</h1>
        </div>
        <div class='description'>
            Personalize your experience.
            <br>
        </div>
        <div class='content'>
            <div class='linkcontainer'>
                    <div class='link'>
                        Theme
                        <form action='index.php' method='get'>
                        <select name='theme'>
                            <?php foreach($this->themes as $k=>$v): if($k == $_SESSION['theme']):?>
                                <option value='<?=$v?>' disabled selected><?=$k?></option>
                                <?php continue; endif ?>
                                <option value='<?=$k?>'><?=$k?></option>
                           <?php endforeach ?>
                        </select>
                        <input type='submit'>
                        </form>
                        <ul>
                            <li>Default - White background and borders with Black text and Blue links.</li>
                            <li>Night - Black background and White text with Blue links.</li>
                            <li>Orange - Orange background and black text with Red links and borders</li>
                        </ul>
                        
                    </div>
                    <div class='link'>
                        Date/Time Format
                        <form action='index.php' method='get'>
                        <select name='dtf'>
                            
                            <?php foreach($this->dt_formats as $k=>$v): if($k == $_SESSION['dtf']):?>
                                <option value='<?=$k?>'selected disabled><?=$k?> 
                                <?php continue;endif ?>
                                <option value='<?=$k?>'><?=$k?></option>
                            <?php endforeach; ?>
                        </select> 
                        <input type='submit'>
                            </form><br>
                        Examples:
                        US - 10-03-2023 01:02 PM<br>
                        UK - 03-10-2023 13:03<br>
                        ISO6801 - 2023-10-03 13:03 +02:00<br>
                        RFC2822 - Mon, 03 Oct 2023 12:02:00 +02:00<br>
                        Human - October 3, 2023, 1:02 PM<br>
                    </div>
            </div>
            
        </div>
    </div>