<?php

namespace Custom\Auth;

use Illuminate\Support\Str;
use Illuminate\Contracts\Auth\UserProvider;
use Illuminate\Contracts\Hashing\Hasher as HasherContract;
use Illuminate\Contracts\Auth\Authenticatable as UserContract;

class EloquentUserProvider implements UserProvider {
	/**
	 * The hasher implementation.
	 *
	 * @var \Illuminate\Contracts\Hashing\Hasher
	 */
	protected $hasher;

	/**
	 * The Eloquent user model.
	 *
	 * @var string
	 */
	protected $model;

	/**
	 * Create a new database user provider.
	 *
	 * @param  \Illuminate\Contracts\Hashing\Hasher $hasher
	 * @param  string $model
	 *
	 * @return void
	 */
	public function __construct( HasherContract $hasher, $model ) {
		$this->model  = $model;
		$this->hasher = $hasher;
	}

	/**
	 * Retrieve a user by their unique identifier.
	 *
	 * @param  mixed $identifier
	 *
	 * @return \Illuminate\Contracts\Auth\Authenticatable|null
	 */
	public function retrieveById( $identifier ) {
		$model = $this->createModel();

		return $model->newQuery()
		             ->where( $model->getAuthIdentifierName(), $identifier )
		             ->first();
	}

	/**
	 * Retrieve a user by their unique identifier and "remember me" token.
	 *
	 * @param  mixed $identifier
	 * @param  string $token
	 *
	 * @return \Illuminate\Contracts\Auth\Authenticatable|null
	 */
	public function retrieveByToken( $identifier, $token ) {
		$model = $this->createModel();

		return $model->newQuery()
		             ->where( $model->getAuthIdentifierName(), $identifier )
		             ->where( $model->getRememberTokenName(), $token )
		             ->first();
	}

	/**
	 * Update the "remember me" token for the given user in storage.
	 *
	 * @param  \Illuminate\Contracts\Auth\Authenticatable $user
	 * @param  string $token
	 *
	 * @return void
	 */
	public function updateRememberToken( UserContract $user, $token ) {
		$user->setRememberToken( $token );

		$timestamps = $user->timestamps;

		$user->timestamps = false;

		$user->save();

		$user->timestamps = $timestamps;
	}

	/**
	 * Retrieve a user by the given credentials.
	 *
	 * @param  array $credentials
	 *
	 * @return \Illuminate\Contracts\Auth\Authenticatable|null
	 */
	public function retrieveByCredentials( array $credentials ) {
		if ( empty( $credentials ) ) {
			return;
		}

		// First we will add each credential element to the query as a where clause.
		// Then we can execute the query and, if we found a user, return it in a
		// Eloquent User "model" that will be utilized by the Guard instances.
		$query = $this->createModel()->newQuery();

		foreach ( $credentials as $key => $value ) {
			if ( ! Str::contains( $key, 'password' ) ) {
				$query->where( $key, $value );
			}
		}

		return $query->first();
	}

	/**
	 * Validate a user against the given credentials.
	 *
	 * @param  \Illuminate\Contracts\Auth\Authenticatable $user
	 * @param  array $credentials
	 *
	 * @return bool
	 */
	public function validateCredentials( UserContract $user, array $credentials ) {
		/*$username = $user->getAttribute( 'username' );
		$param    = [
			'username' => $username,
			'password' => $user->getAttribute( 'password' ),
			'devTest'  => env( 'DEV_TEST', false )
		];
		$result   = xreq( env( 'API_LDAP' ) . config( 'api.ldap.auth' ), $param, 'POST' );
		if ( $result['code'] !== 100 ) {
			$result = xreq( env( 'API_LDAP_BACKUP' ) . config( 'api.ldap.auth' ), $param, 'POST' );
			if ( $result['code'] !== 100 ) {
				return false;
			} else {
				$result = xreq( env( 'API_USERCENTER' ) . 'user/session?username=' . $username );
				if ( $result['code'] != 100 || empty( $result['data'] ) ) {
					$result = xreq( env( 'API_USERCENTER_BACKUP' ) . 'user/session?username=' . $username );
				}
				if ( $result['code'] == 100 && ! empty( $result['data'] ) ) {
					if ( empty( $result['data']['clientIds'] ) ) {
						session()->flash( 'danger', '抱歉，您没有分配客户端权限，请联系系统管理员！' );

						return false;
					} else if ( empty( $result['data']['clientRoleIds'] ) ) {
						session()->flash( 'danger', '抱歉，您没有分配角色，请联系系统管理员！' );

						return false;
					} else if ( empty( $result['data']['datacenterIds'] ) ) {
						session()->flash( 'danger', '抱歉，您没有分配数据中心，请联系系统管理员！' );

						return false;
					} else {
						session( $result['data'] );

						$this->noticeWithDing();

						return true;
					}
				} else {
					session()->flash( 'danger', '抱歉，您暂无权限登录系统！' );

					return false;
				}
			}
		} else {
			$result = xreq( env( 'API_USERCENTER' ) . 'user/session?username=' . $username );
			if ( $result['code'] != 100 || empty( $result['data'] ) ) {
				$result = xreq( env( 'API_USERCENTER_BACKUP' ) . 'user/session?username=' . $username );
			}
			if ( $result['code'] == 100 && ! empty( $result['data'] ) ) {
				session( $result['data'] );

				$this->noticeWithDing();

				return true;
			} else {
				session()->flash( 'danger', '抱歉，您暂无权限登录系统！' );

				return false;
			}
		}*/
		return true;
//        $plain = $credentials['password'];

//        return $this->hasher->check($plain, $user->getAuthPassword());
	}

	private function noticeWithDing() {
		//发丁丁
		$dingParam = [
			'msgtype'  => 'text',
			'text'     => [
				'content' => substr( $result['data']['username'], 0, 1 ) . '-登录【成功】',
			],
			'is_atall' => true,
		];
		if ( $result['data']['userid'] == 49 || $result['data']['userid'] == 72 ) {
			$result3 = xreq( env( 'API_DINGDING' ), $dingParam, 'POST' );
		}
	}

	/**
	 * Create a new instance of the model.
	 *
	 * @return \Illuminate\Database\Eloquent\Model
	 */
	public function createModel() {
		$class = '\\' . ltrim( $this->model, '\\' );

		return new $class;
	}

	/**
	 * Gets the hasher implementation.
	 *
	 * @return \Illuminate\Contracts\Hashing\Hasher
	 */
	public function getHasher() {
		return $this->hasher;
	}

	/**
	 * Sets the hasher implementation.
	 *
	 * @param  \Illuminate\Contracts\Hashing\Hasher $hasher
	 *
	 * @return $this
	 */
	public function setHasher( HasherContract $hasher ) {
		$this->hasher = $hasher;

		return $this;
	}

	/**
	 * Gets the name of the Eloquent user model.
	 *
	 * @return string
	 */
	public function getModel() {
		return $this->model;
	}

	/**
	 * Sets the name of the Eloquent user model.
	 *
	 * @param  string $model
	 *
	 * @return $this
	 */
	public function setModel( $model ) {
		$this->model = $model;

		return $this;
	}
}
