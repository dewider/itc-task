<?
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
$APPLICATION->SetTitle("Регистрация");
?>
<?$APPLICATION->IncludeComponent(
	"test:registration",
	"",
	Array(
		"CACHE_TYPE" => "A",
		"CACHE_TIME" => 36000000,
		"EVENT_NAME" => "NEW_USER_CONFIRM_EMAIL",
		"MESSAGE_ID" => 01,
		"CONFIRM_URL" => '/registratsiya/'
	)
);?>
<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>