<? if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();
$this->setFrameMode(true);

$this->addExternalJS("https://cdnjs.cloudflare.com/ajax/libs/jquery.inputmask/5.0.7/jquery.inputmask.min.js");
?>
<div class="container-fluid regform-wrap">
    <? if ($arResult['IS_CONFIRM'] == 'N') : ?>
        <form action="/" id="regform" data-component-name="<?= $this->getComponent()->getName() ?>" data-component-signed="<?= $this->getComponent()->getSignedParameters() ?>">
            <div class="row">
                <div class="col-md-12">
                    <label for="email"><?= GetMessage("EMAIL") ?></label>
                    <input type="text" id="email" name="email">
                </div>
                <div class="col-md-12">
                    <input type="submit" value="<?= GetMessage("SUBMIT") ?>">
                </div>
                <div class="col-md-12">
                    <span class="message"></span>
                </div>
            </div>
        </form>
    <? elseif ($arResult['IS_CONFIRM'] == 'Y') : ?>
        <? if (isset($arResult['INVALID_HASH']) && $arResult['INVALID_HASH'] == "Y") : ?>
            <div class="row">
                <div class="col-md-12">
                    <span><?= GetMessage("INVALID_HASH") ?></span>
                </div>
            </div>
        <? else : ?>
            <form action="/" id="successform" data-component-name="<?= $this->getComponent()->getName() ?>" data-component-signed="<?= $this->getComponent()->getSignedParameters() ?>">
            <input type="hidden" name="hash" value="<?=$arResult['USER_HASH']?>">
            <div class="row">
                    <div class="col-md-3">
                        <label for="name"><?= GetMessage("NAME") ?></label>
                        <input type="text" id="name" name="name">
                    </div>
                    <div class="col-md-9">
                        <label for="second-name"><?= GetMessage("SECOND_NAME") ?></label>
                        <input type="text" id="second_name" name="second_name">
                    </div>
                    <div class="col-md-12">
                        <label for="tel"><?= GetMessage("TEL") ?></label>
                        <input type="text" class="tel" id="tel" name="tel">
                    </div>
                    <div class="col-md-3">
                        <label for="password"><?= GetMessage("PASSWORD") ?></label>
                        <input type="password" id="password" name="password">
                    </div>
                    <div class="col-md-9">
                        <label for="confirm_password"><?= GetMessage("CONFIRM_PASSWORD") ?></label>
                        <input type="password" id="confirm_password" name="confirm_password">
                    </div>
                    <div class="col-md-12">
                        <input type="submit" value="<?= GetMessage("SUBMIT") ?>">
                    </div>
                    <div class="col-md-12">
                        <span class="message"></span>
                    </div>
                </div>
            </form>
        <? endif ?>
    <? endif; ?>
</div>