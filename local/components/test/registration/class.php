<?

use \Bitrix\Main\Application,
	\Bitrix\Main\UserTable,
	\Bitrix\Main\UI\Extension,
	\Bitrix\Main\Engine\ActionFilter,
	\Bitrix\Main\Localization\Loc,
	\Main\Mail\Event,
	\Bitrix\Main\Engine\Contract\Controllerable;


if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();


class RegistrationComponent extends CBitrixComponent implements Controllerable
{

	public function __construct($component = null)
	{
		parent::__construct($component);
		Extension::load('ui.bootstrap4');
	}

	public function onPrepareComponentParams($params)
	{
		$params = parent::onPrepareComponentParams($params);
		return $params;
	}
	public function configureActions()
	{
		return [
			'add' => [
				'prefilters' => [
					new ActionFilter\Csrf(),
				],
				'postfilters' => []
			]
		];
	}
	protected function listKeysSignedParameters()
	{
		return ['EVENT_NAME', 'MESSAGE_ID', 'CONFIRM_URL'];
	}

	private function checkUserHash($hash)
	{
		$dbUser = UserTable::getList([
			'select' => ['ID'],
			'filter' => [
				'UF_HASH' => $hash
			]
		]);
		if ($arUser = $dbUser->fetch())
			return $arUser['ID'];
		else
			return false;
	}

	public function newUserAction()
	{
		$request = Application::getInstance()->getContext()->getRequest();
		$email = htmlspecialchars($request->get("email"));
		if (!filter_var($email, FILTER_VALIDATE_EMAIL))
			return ['message' => Loc::getMessage('INVALID_EMAIL')];

		$dbUser = UserTable::getList([
			'select' => ['ID'],
			'filter' => [
				'EMAIL' => $email
			]
		]);
		if ($dbUser->fetch())
			return ['message' => Loc::getMessage('EXIST_EMAIL')];

		$hash = md5($email . date("Y-m-d H:i:s"));
		$pass = md5($hash . date("Y-m-d H:i:s"));
		$user = new CUser();
		$uid = $user->Add([
			'LOGIN' => $email,
			'EMAIL' => $email,
			'ACTIVE' => 'N',
			'PASSWORD' => $pass,
			'CONFIRM_PASSWORD' => $pass,
			'UF_HASH' => $hash
		]);

		if (!$uid)
			return ['message' => Loc::getMessage('ADD_ERROR')];

		$server = \Bitrix\Main\Context::getCurrent()->getServer();
		$protocol = ((!empty($server->get['HTTPS']) && $server->get['HTTPS'] != 'off') 
					|| $server->getServerPort() == 443)
						? 'https://' : 'http://';
		$link = $protocol.$server->getServerName()
				.$this->arParams['CONFIRM_URL'] . '?userhash=' . $hash;

		$result = Event::send([    
		    "EVENT_NAME" => $params['EVENT_NAME'],
		    "MESSAGE_ID" => $params['MESSAGE_ID'],
		    "LID" => SITE_ID,
		    "C_FIELDS" => [
		        'LINK' => $link
		    ]
		]);
		if (!$result->isSuccess())
		    return [ 'message' => Loc::getMessage('SEND_ERROR') ];

		return ['message' => Loc::getMessage('ADD_SUCCESS')];
	}

	public function successAction()
	{
		$request = Application::getInstance()->getContext()->getRequest();

		$hash = htmlspecialchars($request->get("hash"));
		$uid = $this->checkUserHash($hash);
		if (!$uid) 
			return ['message' => Loc::getMessage('INVALID_HASH')];

		$requiredFields = [
			'name' => '',
			'second_name' => '',
			'tel' => '',
			'password' => '',
			'confirm_password' => ''
		];
		foreach ($requiredFields as $key => $val) {
			$requiredFields[$key] = htmlspecialchars($request->get($key));
			if (empty($requiredFields[$key]))
				return ['message' => Loc::getMessage('EMPTY_FIELD')];
		}
		if (!preg_match('/((8|\+7)-?)?\(?\d{3,5}\)?-?\d{1}-?\d{1}-?\d{1}-?\d{1}-?\d{1}((-?\d{1})?-?\d{1})?/', $requiredFields['tel']))
			return ['message' => Loc::getMessage('IVALID_TEL')];

		if($requiredFields['password'] !== $requiredFields['confirm_password'])
			return ['message' => Loc::getMessage('IVALID_CONFIRM')];

		$user = new CUser();
		$user->Update($uid, [
			'NAME' => $requiredFields['name'],
			'SECOND_NAME' => $requiredFields['second_name'],
			'PERSONAL_PHONE' => $requiredFields['tel'],
			'PASSWORD' => $requiredFields['password'],
			'CONFIRM_PASSWORD' => $requiredFields['confirm_password'],
			'ACTIVE' => 'Y',
			'UF_HASH' => ''
		]);

		if($err = $user->LAST_ERROR)
			return ['message' => $err];

		return ['message' => Loc::getMessage('SUCCESS_REG')];
	}

	public function executeComponent()
	{
		$request = Application::getInstance()->getContext()->getRequest();
		$hash = htmlspecialchars($request->get("userhash"));
		if (!empty($hash)) {
			$this->arResult['IS_CONFIRM'] = 'Y';
			if ($this->checkUserHash($hash))
				$this->arResult['USER_HASH'] = $hash;
			else
				$this->arResult['INVALID_HASH'] = "Y";
		} else {
			$this->arResult['IS_CONFIRM'] = 'N';
		}

		$this->includeComponentTemplate();
	}
}
