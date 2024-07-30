<?= $this->tag->form(['session/start']) ?>
<fieldset>
    <div>
        <label for='email'>
            Email
        </label>

        <div>
            <?= $this->tag->textField(['email']) ?>
        </div>
    </div>

    <div>
        <label for='password'>
            Password
        </label>

        <div>
            <?= $this->tag->passwordField(['password_md5']) ?>
        </div>
    </div>

    <div>
        <?= $this->tag->submitButton(['Login']) ?>
    </div>
</fieldset>
<?= $this->tag->endform() ?>


