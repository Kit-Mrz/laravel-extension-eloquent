
# laravel extension eloquent

基于 laravel 8.5 的 Eloquent ORM 增强

一般模型：
````PHP
    class Goods extends CrudModel {

    }
````

数据仓库：
````PHP
    class GoodsRepository extends CrudRepository
    {
        public function __construct(Goods $user)
        {
            $this->setModel($user);
        }
    }
````

常用方法：
````PHP
    $goodsRepository->getModel();
    $goodsRepository->setModel();
    $goodsRepository->create();
    $goodsRepository->batchCreate();
    $goodsRepository->retrieve();
    $goodsRepository->update();
    $goodsRepository->updateWithTrashed();
    $goodsRepository->delete();
    $goodsRepository->detailWithTrashed();
````

分表模型：
````PHP
    class Files extends PartitionModel
    {
        /**
         * @var int 分表数
         */
        protected $partitionCount = 64;
    
        /**
         * @var \int[][] 分表配置
         */
        protected $partitionConfig = [
            [
                'partition' => 8,
                'low'       => 0,
                'high'      => 7,
            ],
            [
                'partition' => 16,
                'low'       => 8,
                'high'      => 15,
            ],
            [
                'partition' => 24,
                'low'       => 16,
                'high'      => 23,
            ],
            [
                'partition' => 32,
                'low'       => 24,
                'high'      => 31,
            ],
            [
                'partition' => 40,
                'low'       => 32,
                'high'      => 39,
            ],
            [
                'partition' => 48,
                'low'       => 40,
                'high'      => 47,
            ],
            [
                'partition' => 56,
                'low'       => 48,
                'high'      => 55,
            ],
            [
                'partition' => 64,
                'low'       => 56,
                'high'      => 63,
            ],
        ];
    
    
        public function getPartitionCount() : int
        {
            return $this->partitionCount;
        }
    
        public function getPartitionConfig() : array
        {
            return $this->partitionConfig;
        }
    
    }
````

数据仓库：
````PHP
    class HistorySearchRepository extends PartitionCrudRepository {
        public function __construct(HistorySearch $historySearch)
        {
            $this->setModel($historySearch);
        } 
    }
````

JWT鉴权模型
````PHP
    class User extends CrudModel implements JWTSubject, AuthenticatableContract, AuthorizableContract, CanResetPasswordContract
    {
        use HasFactory, Notifiable, SoftDeletes, Authenticatable, Authorizable, CanResetPassword, MustVerifyEmail;

        /**
         * 该表将与模型关联。
         *
         * @var string
         */
        protected $table = 'users';

        /**
         * The attributes that are mass assignable.
         *
         * @var array
         */

        protected $fillable = [
            'created_by', 'updated_by', 'deleted_by',
            'created_at', 'updated_at', 'deleted_at',
        ];

        /**
         * The attributes that should be hidden for arrays.
         *
         * @var array
         */
        protected $hidden = [
        ];

        /**
         * The attributes that should be cast to native types.
         *
         * @var array
         */
        protected $casts = [
            //'email_verified_at' => 'datetime',
        ];

        /**
         * Get the identifier that will be stored in the subject claim of the JWT.
         *
         * @return mixed
         */
        public function getJWTIdentifier()
        {
            return $this->getKey();
        }

        /**
         * Return a key value array, containing any custom claims to be added to the JWT.
         *
         * @return array
         */
        public function getJWTCustomClaims()
        {
            return [];
        }

        /**
         * Determine if the user has verified their email address.
         *
         * @return bool
         */
        public function hasVerifiedEmail()
        {
            return true;
        }

        /**
         * Mark the given user's email as verified.
         *
         * @return bool
         */
        public function markEmailAsVerified()
        {
            return true;
        }

        /**
         * Send the email verification notification.
         *
         * @return void
         */
        public function sendEmailVerificationNotification()
        {
        }

        /**
         * Get the email address that should be used for verification.
         *
         * @return string
         */
        public function getEmailForVerification()
        {
            return '';
        }
    }
````
