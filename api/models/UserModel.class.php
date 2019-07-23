<?php
namespace models;

use libs\Model;
use Emarref\Jwt\Claim;

class UserModel extends Model{

	protected $key = "";
	protected $expire = -1;

	public function __construct(){

		parent::__construct();
		$this->key = config("userToken.accessKey");
		$this->expire = config("userToken.expire");
	
	}

	public function getUserInfoByToken($token=''){

		return $this->query("select * from __table__ where usertoken=? limit 1",[$token]);

	}

	public function getUserInfoByUsername($username=''){

		return $this->query("select * from __table__ where username=? limit 1",[$username]);

	}

	protected function getUserInfoByUid($uid = 1) {

		return $this->query("select * from __table__ where id=? limit 1",[$uid]);

	}

	public function createToken($user){
		$token = new \Emarref\Jwt\Token(); // 实例化token
		// 
		$token->addClaim(new Claim\Audience(['audience_1', 'audience_2']));
		$token->addClaim(new Claim\Expiration(new \DateTime($this->expire ." seconds")));
		$token->addClaim(new Claim\IssuedAt(new \DateTime('now')));
		$token->addClaim(new Claim\Issuer('zhouguoqiang'));
		$token->addClaim(new Claim\JwtId('1'));
		$token->addClaim(new Claim\NotBefore(new \DateTime('now')));
		$token->addClaim(new Claim\Subject('http://www.apitest.com'));

		$token->addClaim(new Claim\PrivateClaim('user', $user));


		$jwt = new \Emarref\Jwt\Jwt();
		$algorithm = new \Emarref\Jwt\Algorithm\Hs256($this->key);
		$encryption = \Emarref\Jwt\Encryption\Factory::create($algorithm);
		$serializedToken = $jwt->serialize($token, $encryption);

		return $serializedToken;
	}

	public function verifyToken($token=""){
		$jwt = new \Emarref\Jwt\Jwt();
		$token = $jwt->deserialize($token);
		// 实例化上下文
		$algorithm = new \Emarref\Jwt\Algorithm\Hs256($this->key);
		$encryption = \Emarref\Jwt\Encryption\Factory::create($algorithm);

		$context = new \Emarref\Jwt\Verification\Context($encryption);
		$context->setAudience('audience_1');
		$context->setIssuer('zhouguoqiang');
		$context->setSubject('http://www.apitest.com');

		try {
		    $jwt->verify($token, $context);
		} catch (\Emarref\Jwt\Exception\VerificationException $e) {
		    return $e->getMessage();
		}
		// 如果验证通过，返回用户的id
		$user = $token->getPayload()->findClaimByName("user")->getValue();
		// 处理user信息
		$user = json_decode(decrypt($user,config("encrypt.key"),config("encrypt.iv")),true);
		// 返回用户信息
		return $this->getUserInfoByUid($user['id']) ?? null;

	}


}