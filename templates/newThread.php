<body>
    <div class='board'>
        <div class='header'> 
            <h1><a href='../'><</a> Create a new thread</h1>
        </div>
        <div class='description'>
            Remember to read and follow the rules and formatting guidelines for the board you are going to post to, and the global rules.
            <br><br>
            search: <input type='text' name='q' required><input type='submit' value='Search'><a href='?i=r'> rules</a> | <a href='?i=h'>support</a> | <a href='?i=s'>&#9881;</a>
            <br>
        </div>
        <div class='content'>
            <div class='threadcontainer'>
                <div class='reply'>
                        <div class='replycontent'>
                            <form action='upload.php' method='POST' accept-charset="UTF-8">
                                <input type='hidden' name='captcha_answer' value='<?=$_SESSION['captcha_answer'] ?>'>
                                <input type='hidden' name='csrf_token' value='<?=$_SESSION['csrf_token'] ?>'>
                                <input type='hidden' name='bot' value=''>
                                <table>
                                    <tr>
                                        <td class='title'>Alias</td>
                                        <td class='felem'><input type='text' name='poster' placeholder="Anonymous" value="Anonymous" required></td>
                                        <td class='tip'>Defaults to 'Anonymous'</td>
                                    </tr>
                                    <tr>
                                        <td class='title'>Board</td>
                                        <td class='felem'>
                                            <select name='board'>
                                                <?php foreach($this->boards as $board): ?>
                                                    <option value='<?=$board->getLink()?>'><?=$board->getLink() ?> - <?=$board->getTitle()?></option>
                                                <?php endforeach ?>
                                            </select>
                                        </td>
                                        <td class='tip'>Choose the board to publish to</td>
                                    </tr>
                                    <tr>
                                        <td class='title'>Subject</td>
                                        <td class='felem'><input type='text' name='subject' placeholder='Ouch!'></td>
                                        <td class='tip'>Short and snappy to lure in the readers.</td>
                                    </tr>
                                    <tr>
                                        <td class='title'>Content</td>
                                        <td class='felem'><textarea name='content' required>A bee bit my bottom, now my bottom is big!</textarea></td>
                                        <td class='tip'>You must comply to the rules and formatting of this board.</td>
                                    </tr>
                                    <tr>
                                        <td class='title'>Magnet</td>
                                        <td class='felem'><input type='url' name='magnet' placeholder='Ouch!'></td>
                                        <td class='tip'>Use the prefix 'magnet:'</td>
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
                </div>
            </div>
        </div>
    </div>
</body>