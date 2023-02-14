<?php

namespace App\Http\Controllers;

use App\Repositories\SignUpRepository;
use App\Traits\Utillity;
use Illuminate\Http\Request;
use Validator;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Config;
use Twilio\Rest\Client;

class MailsSmsController extends Controller
{
    use Utillity;
    public function mailsSignUpSms()
    {
        config()->set('welcomeTemplateId', 'd-b543dcee9baa4981bddcf304c6505a85');
        config()->set('guestwelcomeTemplateId', 'd-d473d93ea3244077a7c552721e339d19');
        config()->set('affiliatewelcomeTemplateId', 'd-dee4db46055d49dc99ab96d3da643f72');
        config()->set('level2store', 'd-cca78e06c1cc47b2884cca29a3a950e9');
        config()->set('level3store', 'd-996c8aa4ce284d91bf5223000f38bdf1');
        config()->set('membershipTemplateID', 'd-47dbc83a15564077bafb6dcca194bf74');
        config()->set('renewalmembershipTemplateID', 'd-470756f3a8dd4c8ea2ac8403fb5186f8');
        config()->set('referralshipTemplateID', 'd-32fbcee6965b41b092b2416577a44ff7');
        config()->set('referralmainotification', 'd-d6dfdbe95fc24fc9aceb67647ab62467');
        config()->set('Renewal_failureTemplateID', 'd-ce4b3f4fa1304b33aa38e21e5bc77f81');
        config()->set('mallownerwelcomemail', 'd-2f9a0dbaedea4ef6b20cae815ef45bd0');
        config()->set('mallownermembership', 'd-1b6e86f4fa544948ba355dd2c61fdaf8');
        config()->set('storeownerlead', 'd-2db7b31e9d954447996b2b428cd918ac');
        config()->set('applink', 'd-82cde1889eb14cf4b60fd261957876ce');
        //forgot password template id
        config()->set('forgotPasswordTemplateId', 'd-86940296f9cd43228e39edd5fc93570f');
        // Membership template id
        config()->set('membershipWarningTemplateID', 'd-73c03c460b58497984c444c8b2e494bd');
        config()->set('membershipExpiryTemplateID', 'd-fd8bcf065dcd4e86aeb8cd1b68072796');
        config()->set('autornewalcancellsupportnTemplateID', 'd-b8e08ed02c0b47fd8f5eb7055c35a867');
        config()->set('membershipUpgradeTemplateID', "d-c58a5e74591c4dfa997def7f3db3639d");
        config()->set('membershipUpgradeSupportTemplateID', 'd-63ab266aa88f412ea698da381255066a');
        config()->set('membershipCancelledTemplateID', 'd-d0f596073ee74a059880152ec0cbe1a7');
        config()->set('membershipDowngradeTemplateID', 'd-7871cdb92cd545fbafcf5df3575f20be');
        config()->set('membershipDowngradeSupportTemplateID', 'd-9796b92e791944fab9545b9bb3f6e11f');
        config()->set('vendorQueryTemplateID', 'd-f8255b62e2dd44728cb5f819d8fb95e8');
        config()->set('mallOwnerGracePeriod', 'd-8d6ddc8a3bf74a1a8159d272d7d12ded');
        // Travertex Template ID

        //URL
        if ($_SERVER['HTTP_HOST'] == 'localhost' || $_SERVER['HTTP_HOST'] == '127.0.0.1:8000') {
            config()->set('redirecturl', 'http://localhost/bemodo_project/bemodo/login-page?message=success');
        } else {
            config()->set('redirecturl', 'https://'.$_SERVER['HTTP_HOST'].'/login-page?message=success');
        }
       
        $action   =  $_REQUEST['action'];
        if(isset($action) &&  $action == "Customer"): // SignUp Mails
            $this->signupCustomer();
        elseif(isset($action) && $action == "signup_storeowner"):
            $this->signupStoreOwner();
        elseif(isset($action) && $action == "autoRenewal"):
            $this->autoRenewal();
        elseif(isset($action) && $action == 'userreferral'):
            $this->userReferral();
        elseif(isset($action) && $action == 'referral-notification'):
            $this->referralNotification();
        elseif(isset($action) && $action == 'autoRenewal_failure'):
            $this->autoRenewalFailure();
        elseif(isset($action) && $action == 'signup_mallowner'):
            $this->signupMallOwner();
        elseif(isset($action) && $action == 'storeowner_leads'):
            $this->storeOwnerLeads();
        elseif(isset($action) && $action == 'ForgotPassword'): // Forgot Password Mail
            $this->ForgotPassword();
        elseif(isset($action) && $action == 'Membership_warning_email'): // MemberShip Mails
            $this->MembershipWarningMail();
        elseif(isset($action) && $action == 'Membership_expiry_email'):
            $this->MembershipExpiryMail();
        elseif(isset($action) && $action == 'autorenewal_cancellation'):
            $this->autoRenewalCancellationMail();
        elseif(isset($action) && $action == 'customer_vip'):
            $this->customerVip();
        elseif(isset($action) && $action == 'customer_storeowner'):
            $this->customerStoreOwner();
        elseif(isset($action) && $action == 'vip_storeowner'):
            $this->vipStoreOwner();
        elseif(isset($action) && $action == 'storeowner_storeowner'):
            $this->storeOwnerStoreOwner();
        elseif(isset($action) && $action == 'upgrade_to_mallowner'):
            $this->upgradeToMallOwner();
        elseif(isset($action) && $action == 'account_cancellation_email'):
            $this->accountCancellation();
        elseif(isset($action) && $action == 'account_downgrade'):
            $this->accountDowngrade();
        elseif(isset($action) && $action == 'vendor_query'):
            $this->vendorQuery();
        elseif(isset($action) && $action == 'membership_grace_period'):
            $this->membershipGracePeriod();
        endif;
        
    }
    /**
     * @OA\POST(
     *     path="/api/V1/mailSignupSms/mailSmsPage?action=Customer",
     *      @OA\Parameter(
     *      name="params",
     *      in="query",
     *      @OA\Schema(
     *      type="object",
     * @OA\Property(property="custom_mail", type="string"),
     * @OA\Property(property="FirstName", type="string"),
     * @OA\Property(property="LastName", type="string"),
     * @OA\Property(property="membership_type", type="string"),
     * @OA\Property(property="Phone1", type="integer"),
     * @OA\Property(property="Password", type="string"),
     * @OA\Property(property="signupType", type="string")
     * )),
     *     @OA\Response(response=200, description="Success"),
     *     @OA\Response(response=401, description="Unauthenticated"),
     *     @OA\Response(response=403, description="Forbidden"),
     *     @OA\Response(response=404, description="Not Found")
     * )
     */
    public function signupCustomer()
    {
        $params         =  $this->getData($_REQUEST);
        $to             =  $params["custom_mail"];
        $customer_name  =  $params['FirstName']." ".$params['LastName'];
        $customer_type  =  $params["membership_type"];
        $mobile         =  $params["Phone1"];
        $guestpassword  =  $params["Password"];
        $signupType     =  $params["signupType"];

        $cus_membership = date("m/d/Y");
        $startdate      = date("m-d-Y");
        $enddate        = "N/A";
        $amount         = "Free";
        if ($signupType != "checkout") {
            $guestpassword = "";
        }
        if (empty($guestpassword)) {
            $template_id = config("welcomeTemplateId");
        } else {
            $template_id = config("guestwelcomeTemplateId");
        }
        $pageurl = config("redirecturl");
        $startselling = "Start Shopping!!";
        $user = [
            "customer_name"     => $customer_name,
            "customer_type"     => $customer_type,
            "email"             => $to,
            "redirect_login"    => $pageurl,
            "mobile"            => $mobile,
            "guestpassword"     => $guestpassword
        ];

        $member = [
            "username"          => $customer_name,
            "package_name"      => $customer_type,
            "email"             => $to,
            "start_date"        => $cus_membership,
            "end_date"          => $enddate,
            "redirect_login"    => $pageurl,
            "amount"            => $amount,
            "startselling"      => $startselling
        ];
        try {
            Mail::to($to)->send(new \App\Mail\MyTestMail($user, $template_id));
            Mail::to($to)->send(new \App\Mail\MemberMail($member));
            $this->sendMembershipeSms($customer_name, $customer_type, $startdate, $enddate, $mobile, $amount);
            $this->sendCustomerWelcomeSms($customer_name, $mobile);
        } catch (Response $e) {
            echo 'Caught exception: '.  $e->getMessage(). "\n";
        }
        
    }
    public function sendCustomerWelcomeSms($customer_name, $mobile)
    {
        $receiverNumber = $mobile;
        $content = "Hi $customer_name! Thanks for signing up with Bemodo.com.";
        if ($_SERVER['HTTP_HOST'] == 'localhost' || $_SERVER['HTTP_HOST'] == '127.0.0.1:8000' || $_SERVER['HTTP_HOST'] == 'bemodonew.wpengine.com') {
            $mobileno =    '+91'.$mobile;  
        } else {
            $mobileno =    '+1'.$mobile;
        }
        try {
  
            $account_sid = getenv("TWILIO_SID");
            $auth_token = getenv("TWILIO_TOKEN");
            $twilio_number = getenv("TWILIO_FROM");
  
            $client = new Client($account_sid, $auth_token);
            $client->messages->create($receiverNumber, [
                'from' => $twilio_number, 
                'body' => $content]);
  
            dd('SMS Sent Successfully.');
  
        } catch (Response $e) {
            dd("Error: ". $e->getMessage());
        }

    }
    public function sendMembershipeSms($customer_name, $customer_type, $startdate, $enddate, $mobile, $price)
    {
        
        if ($customer_type == "Customer") {
            $price = "0";
        }
        $content = "Hi $customer_name! Thanks for signing up with Bemodo.com. Your Membership details are: Membership Type :  $customer_type Valid from $startdate to $enddate Amount Paid : $price";
        if ($_SERVER['HTTP_HOST'] == 'localhost' || $_SERVER['HTTP_HOST'] == 'bemododev.wpengine.com') {
            $mobileno =    '+91'.$mobile;
        } else {
            $mobileno =    '+1'.$mobile;
        }

        try {
  
            $account_sid = getenv("TWILIO_SID");
            $auth_token = getenv("TWILIO_TOKEN");
            $twilio_number = getenv("TWILIO_FROM");
  
            $client = new Client($account_sid, $auth_token);
            $client->messages->create($mobileno, [
                'from' => $twilio_number, 
                'body' => $content]);
  
            dd('SMS Sent Successfully.');
  
        } catch (Response $e) {
            dd("Error: ". $e->getMessage());
        }
    }
    /**
     * @OA\POST(
     *     path="/api/V1/mailSignupSms/mailSmsPage?action=signup_storeowner",
     *      @OA\Parameter(
     *      name="params",
     *      in="query",
     *      @OA\Schema(
     *      type="object",
     * @OA\Property(property="custom_mail", type="string"),
     * @OA\Property(property="FirstName", type="string"),
     * @OA\Property(property="LastName", type="string"),
     * @OA\Property(property="packageName", type="string"),
     * @OA\Property(property="siteUrl", type="string"),
     * @OA\Property(property="mobilenum", type="integer"),
     * @OA\Property(property="amount", type="integer")
     * )),
     *     @OA\Response(response=200, description="Success"),
     *     @OA\Response(response=401, description="Unauthenticated"),
     *     @OA\Response(response=403, description="Forbidden"),
     *     @OA\Response(response=404, description="Not Found")
     * )
     */
    public function signupStoreOwner()
    {
        $params  =  $this->getData($_REQUEST);
        $customer_name  =  $params['FirstName']." ".$params['LastName'];
        $to = $params["custom_mail"];
        $package = $params["packageName"];
        $site = $params["siteUrl"];
        $mobile=$params["mobilenum"];
        $price = "$"." ".$params["amount"];
        $pageurl = config("redirecturl");
        if ($package == "SSO") {
            $price = "$ 0";
            $startdate = date("m/d/Y");
            $enddate = "N/A";
            $customer_type = "Starter Store Owner";
            $this->sendapplink($to);
        } elseif ($package == "ASOM") {
            $customer_type = "Advanced Store Owner - 1 Month";
            $price = "$ 99.95";
            $startdate = date("m/d/Y");
            $enddate = date('m/d/Y', strtotime($startdate. '1 month'));
            $this->sendapplink($to);
        } elseif ($package == "PSOM") {
            $customer_type = "Pro Store Owner - 1 Month";
            $price = "$ 199.95";
            $startdate = date("m/d/Y");
            $enddate = date('m/d/Y', strtotime($startdate. '1 month'));
            $this->sendapplink($to);
        } elseif ($package == "ASOMY") {
            $customer_type = "Advanced Store Owner - 1 Year";
            $price = "$ 999.95";
            $startdate = date("m/d/Y");
            $enddate = date('m/d/Y', strtotime($startdate. '12 month'));
            $this->sendapplink($to);
        } elseif ($package == "PSOMY") {
            $customer_type = "Pro Store Owner - 1 Year";
            $price = "$ 1999.95";
            $startdate = date("m/d/Y");
            $enddate = date('m/d/Y', strtotime($startdate. '12 month'));
            $this->sendapplink($to);
        } elseif ($package == "MVP") {
            $customer_type = "VIP Customer -1 Month";
            $price = "$"." ".$params["amount"];
            $startdate = date("m/d/Y");
            $enddate = date('m/d/Y', strtotime($startdate. '1 month'));
        } elseif ($package == "MVA") {
            $customer_type = "VIP Customer -12 Month";
            $price = "$"." ".$params["amount"];
            $startdate = date("m/d/Y");
            $enddate = date('m/d/Y', strtotime($startdate. '12 month'));
        }


        if ($package == "SSO") {
            $leveltemplate = config("affiliatewelcomeTemplateId");
        } elseif ($package == "ASOM" || $package == "ASOMY") {
            $leveltemplate = config("level2store");
        } else {
            $leveltemplate = config("level3store");
        }

        $sOWelcomeMail = [
            "customer_name" => $customer_name,
            "customer_type" => $customer_type,
            "email" => $to,
            "bemodo_URL" => $site,
            "redirect_login" => $pageurl
        ];
        if($customer_type == "Starter Store Owner" || $customer_type == "Advanced Store Owner - 1 Month" || $customer_type == "Pro Store Owner - 1 Month" || $customer_type == "Advanced Store Owner - 1 Year" || $customer_type == "Pro Store Owner - 1 Year" || $customer_type == "Mall Owner - 1 Month" || $customer_type == "Mall Owner - 1 Year") {
            $startselling = "Start Selling!!";
        } else {
            $startselling = "Start Shopping!!";
        }
        $memberShipMail = [
            "username" => $customer_name,
            "package_name" => $customer_type,
            "email" => $to,
            "bemodo_URL" => $site,
            "start_date" => $startdate,
            "end_date" => $enddate,
            "redirect_login" => $pageurl,
            "amount" => $price,
            "startselling" => $startselling
        ];
        Mail::to($to)->send(new \App\Mail\StoreOwnerWelcomeMail($sOWelcomeMail, $leveltemplate));
        Mail::to($to)->send(new \App\Mail\SendRepMembershipEmail($memberShipMail));
        //$this->sendWelcomeSms($customer_name, $site, $mobile);
        //$this->sendMembershipeSms($customer_name, $customer_type, $startdate, $enddate, $mobile, $price);
    }
    public function sendWelcomeSms($customer_name, $site, $mobile)
    {
        $content = "Hi $customer_name! Thanks for signing up with Bemodo.com. We have created your personalized shop at BemodoShop LLC. You can access it at :$site";
        if ($_SERVER['HTTP_HOST'] == 'localhost' || $_SERVER['HTTP_HOST'] == 'bemododev.wpengine.com') {
            $mobileno =    '+91'.$mobile;
        } else {
            $mobileno =    '+1'.$mobile;
        }

        try {
  
            $account_sid = getenv("TWILIO_SID");
            $auth_token = getenv("TWILIO_TOKEN");
            $twilio_number = getenv("TWILIO_FROM");
  
            $client = new Client($account_sid, $auth_token);
            $client->messages->create($mobileno, [
                'from' => $twilio_number,
                'body' => $content]);
  
            dd('SMS Sent Successfully.');
  
        } catch (Response $e) {
            dd("Error: ". $e->getMessage());
        }
    }
       /**
     * @OA\POST(
     *     path="/api/V1/mailSignupSms/mailSmsPage?action=autoRenewal",
     *      @OA\Parameter(
     *      name="params",
     *      in="query",
     *      @OA\Schema(
     *      type="object",
     * @OA\Property(property="email", type="string"),
     * @OA\Property(property="name", type="string"),
     * @OA\Property(property="packageName", type="string"),
     * @OA\Property(property="price", type="integer"),
     * @OA\Property(property="phoneNumber", type="integer"),
     * )),
     *     @OA\Response(response=200, description="Success"),
     *     @OA\Response(response=401, description="Unauthenticated"),
     *     @OA\Response(response=403, description="Forbidden"),
     *     @OA\Response(response=404, description="Not Found")
     * )
     */
    public function autoRenewal()
    {
        $params         =  $this->getData($_REQUEST);
        $amount         = $params['price'];
        $to             = $params['email'];
        $mobile         = $params['phoneNumber'];
        $customer_name  = $params['name'];
        $package        = $params['packageName'];
        $pageurl = config("redirecturl");
        if ($package == "MVA") {
            $startdate = date("m/d/Y");
            $enddate = date('m/d/Y', strtotime($startdate. '12 month'));
            $customer_type = "VIP Customer -12 Month";
        } elseif ($package == "MVP") {
            $startdate = date("m/d/Y");
            $enddate = date('m/d/Y', strtotime($startdate. '1 month'));
            $customer_type = "VIP Customer -1 Month";
        } elseif ($package == "ASOM" || $package == "PSOM") {
            $startdate = date("m/d/Y");
            $enddate = date('m/d/Y', strtotime($startdate. '1 month'));
            if ($package == "ASOM") {
                $customer_type = "Advanced Store Owner - 1 Month";
            } elseif ($package == "PSOM") {
                $customer_type = "Pro Store Owner - 1 Month";
            }
        } elseif ($package == "ASOMY" || $package == "PSOMY") {
            $startdate = date("m/d/Y");
            $enddate = date('m/d/Y', strtotime($startdate. '12 month'));
            if ($package == "ASOMY") {
                $customer_type = "Advanced Store Owner - 1 Year";
            } elseif ($package == "PSOMY") {
                $customer_type = "Pro Store Owner - 1 Year";
            }
        } elseif ($package == "MMO") {
            $startdate = date("m/d/Y");
            $enddate = date('m/d/Y', strtotime($startdate. '1 month'));
            $customer_type = "Mall Owner - 1 Month";
        }
        $autoMemberShip = [
            "username" => $customer_name,
            "startdate" => $startdate,
            "enddate" => $enddate,
            "package_name" => $customer_type,
            "amount" => $amount,
            "pageurl" => $pageurl
        ];
        Mail::to($to)->send(new \App\Mail\SendAutoMembershipEmail($autoMemberShip));
        //$this->sendMembershipeSms($customer_name, $customer_type, $startdate, $enddate, $mobile, $amount);
    }
    /**
     * @OA\POST(
     *     path="/api/V1/mailSignupSms/mailSmsPage?action=userreferral",
     *      @OA\Parameter(
     *      name="params",
     *      in="query",
     *      @OA\Schema(
     *      type="object",
     * @OA\Property(property="email", type="string"),
     * @OA\Property(property="link", type="string"),
     * @OA\Property(property="customerType", type="string"),
     * @OA\Property(property="firstName", type="string"),
     * @OA\Property(property="lastName", type="string"),
     * @OA\Property(property="mallName", type="string"),
     * )),
     *     @OA\Response(response=200, description="Success"),
     *     @OA\Response(response=401, description="Unauthenticated"),
     *     @OA\Response(response=403, description="Forbidden"),
     *     @OA\Response(response=404, description="Not Found")
     * )
     */
    public function userReferral()
    {
        $params         =  $this->getData($_REQUEST);
        $to = $params['email'];
        $link = $params['link'];
        $customertype = $params['customerType'];
        $customername = $params['firstName'].' '.$params['lastName'];
        $content = "I have referred you to Bemodo. Its an unique shopping experience, where you get rewarded for the products that you purchase. What more - You can earn rewards for the purchase made by your referrals.";
        if ($customertype == "Mall Owner" || $customertype == "Standard Mall Owner") {
            $mallname = $params['mallName'];
            $mall = "Mall Name:"." ".$mallname;
            $content = "I have referred you to Bemodo. Bemodo is a platform that you can use as a stepping stone to launch your online business. Take advantage of a large selection of popular, name brand products while also having the ability to sell your own on the same site!";
        }
        $referral = [
            "link" => $link,
            "content" => $content,
            "name" => $customername,
            "mall" => $mall
        ];
        Mail::to($to)->send(new \App\Mail\UserReferralMail($referral));
    }
    /**
     * @OA\POST(
     *     path="/api/V1/mailSignupSms/mailSmsPage?action=referral-notification",
     *      @OA\Parameter(
     *      name="params",
     *      in="query",
     *      @OA\Schema(
     *      type="object",
     * @OA\Property(property="email", type="string"),
     * @OA\Property(property="fname", type="string"),
     * @OA\Property(property="lname", type="string"),
     * @OA\Property(property="type", type="string"),
     * )),
     *     @OA\Response(response=200, description="Success"),
     *     @OA\Response(response=401, description="Unauthenticated"),
     *     @OA\Response(response=403, description="Forbidden"),
     *     @OA\Response(response=404, description="Not Found")
     * )
     */
    public function referralNotification()
    {
        $params   =  $this->getData($_REQUEST);
        $to = $params['email'];
        $customername = $params['fname']." ".$params['lname'];
        $member_type = $params['type'];
        $referralNotification = [
            "name" => $customername,
            "type" => $member_type
        ];
        Mail::to($to)->send(new \App\Mail\ReferralNotificationMail($referralNotification));
    }
    /**
     * @OA\POST(
     *     path="/api/V1/mailSignupSms/mailSmsPage?action=autoRenewal_failure",
     *      @OA\Parameter(
     *      name="params",
     *      in="query",
     *      @OA\Schema(
     *      type="object",
     * @OA\Property(property="email", type="string"),
     * @OA\Property(property="phoneNumber", type="integer"),
     * @OA\Property(property="name", type="string"),
     * @OA\Property(property="packageName", type="string")
     * )),
     *     @OA\Response(response=200, description="Success"),
     *     @OA\Response(response=401, description="Unauthenticated"),
     *     @OA\Response(response=403, description="Forbidden"),
     *     @OA\Response(response=404, description="Not Found")
     * )
     */
    public function autoRenewalFailure()
    {
        $params   =  $this->getData($_REQUEST);
        $to = $params['email'];
        $mobile = $params['phoneNumber'];
        $customer_name = $params['name'];
        $package = $params['packageName'];
        $renewalFailure = [
            "username" => $customer_name,
            "package_name" => $package
        ];
        Mail::to($to)->send(new \App\Mail\AutoRenewalFailureMail($renewalFailure));
        //$this->autoRenewalFailureSms($customer_name, $mobile);
    }
    public function autoRenewalFailureSms($customer_name, $mobile)
    {
        $content = "Hey $customer_name, We couldn’t complete a charge on your payment card to process your Bemodo Membership fee. Please visit your 'My Profile' page to Renew your membership";
        if ($_SERVER['HTTP_HOST'] == 'localhost' || $_SERVER['HTTP_HOST'] == 'bemododev.wpengine.com') {
            $mobileno =    '+91'.$mobile;
        } else {
            $mobileno =    '+1'.$mobile;
        }

        try {
  
            $account_sid = getenv("TWILIO_SID");
            $auth_token = getenv("TWILIO_TOKEN");
            $twilio_number = getenv("TWILIO_FROM");
  
            $client = new Client($account_sid, $auth_token);
            $client->messages->create($mobileno, [
                'from' => $twilio_number, 
                'body' => $content]);
  
            dd('SMS Sent Successfully.');
  
        } catch (Response $e) {
            dd("Error: ". $e->getMessage());
        }
    }
    /**
     * @OA\POST(
     *     path="/api/V1/mailSignupSms/mailSmsPage?action=signup_mallowner",
     *      @OA\Parameter(
     *      name="params",
     *      in="query",
     *      @OA\Schema(
     *      type="object",
     * @OA\Property(property="custom_mail", type="string"),
     * @OA\Property(property="firstname", type="string"),
     * @OA\Property(property="lastname", type="string"),
     * @OA\Property(property="amount", type="integer"),
     * @OA\Property(property="mobilenum", type="integer"),
     * @OA\Property(property="siteUrl", type="string"),
     * @OA\Property(property="couponStatus", type="string"),
     * @OA\Property(property="couponName", type="string"),
     * @OA\Property(property="packageName", type="string")
     * )),
     *     @OA\Response(response=200, description="Success"),
     *     @OA\Response(response=401, description="Unauthenticated"),
     *     @OA\Response(response=403, description="Forbidden"),
     *     @OA\Response(response=404, description="Not Found")
     * )
     */
    public function signupMallOwner()
    {
        $params   =  $this->getData($_REQUEST);
        $to = $params["custom_mail"];
        $package = $params["packageName"];
        $site = $params["siteUrl"];
        $mobile= $params["mobilenum"];
        $couponstatus = $params["couponStatus"];
        $couponName = $params["couponName"];
        $customer_name = $params["firstname"]." ".$params["lastname"];
        $price = "$"." ".$params["amount"];
        $startdate = date("m/d/Y");
        $pageurl = config("redirecturl");
        if ($package == "MMO") {
            if ($couponstatus == "yes" && $couponName == "BEMMALLNEW") {
                $customer_type = "Mall Owner - 2 Month";
                $enddate = date('m/d/Y', strtotime($startdate. '2 month'));
                $couponoffer = "The first 2 months of your Mall Owner membership is absolutely FREE!!";
            } elseif ($couponstatus == "yes" && $couponName == "BEMMALLSET") {
                $customer_type = "Mall Owner - 1 Month";
                $enddate = date('m/d/Y', strtotime($startdate. '1 month'));
                $couponoffer = "Your Mall Setup fee is Free!!";
            } elseif ($couponstatus == "yes" && $couponName == "BEMMALLFREE") {
                $customer_type = "Mall Owner - 2 Month";
                $enddate = date('m/d/Y', strtotime($startdate. '2 month'));
                $couponoffer = "Your Mall Setup fee is Free. Your monthly fee is waived for the first 2 months!!";
            } elseif ($couponstatus == "yes" && $couponName == "MallFndr22") {
                $customer_type = "Mall Owner - 1 Month";
                $enddate = date('m/d/Y', strtotime($startdate. '1 month'));
                $couponoffer = "Your mall setup fee is Free. You will be charged $149 for your 1st monthly fee and after the 1st month you will be charged the regular fee of $299.95 every month!!";
            } else {
                $customer_type = "Mall Owner - 1 Month";
                $enddate = date('m/d/Y', strtotime($startdate. '1 month'));
            }
        } elseif ($package == "SMO") {
            if ($couponstatus == "yes") {
                $customer_type = "Mall Owner";
                $enddate = date('m/d/Y', strtotime($startdate. '2 month'));
                $couponoffer = "The first 2 months of your Mall Owner membership is absolutely FREE!!";
            } else {
                $customer_type = "Mall Owner - 1 Year";
                $enddate = date('m/d/Y', strtotime($startdate. '12 month'));
            }
        }
        $mallOwnerWelcome = [
            "customer_name" => $customer_name,
            "customer_type" => $customer_type,
            "email" => $to,
            "bemodo_URL" => $site,
            "redirect_login" => $pageurl

        ];
        $mallOwnerMembership = [
            "username" => $customer_name,
            "package_name" => $customer_type,
            "email" => $to,
            "bemodo_URL" => $site,
            "start_date" => $startdate,
            "end_date" => $enddate,
            "redirect_login" => $pageurl,
            "amount" => $price,
            "couponoffer" => $couponoffer
        ];
        Mail::to($to)->send(new \App\Mail\SendMallOwnerWelcomeEmail($mallOwnerWelcome));
        Mail::to($to)->send(new \App\Mail\SendMallOwnerMembershipEmail($mallOwnerMembership));
        //$this->sendWelcomeSms($customer_name, $site, $mobile);
        //$this->sendMembershipeSms($customer_name, $customer_type, $startdate, $enddate, $mobile, $price);

    }
    /**
     * @OA\POST(
     *     path="/api/V1/mailSignupSms/mailSmsPage?action=storeowner_leads",
     *     @OA\Response(response=200, description="Success"),
     *     @OA\Response(response=401, description="Unauthenticated"),
     *     @OA\Response(response=403, description="Forbidden"),
     *     @OA\Response(response=404, description="Not Found")
     * )
     */
    public function storeOwnerLeads()
    {
       // $to = "marketing@bemodo.com";
        $to = "kana123@yopmail.com";
        $testemail = $this->getData($_POST);
        $allleads = implode(", ", $testemail);
        $storeOwnerLeads = [
            "email" => $allleads
        ];
        Mail::to($to)->send(new \App\Mail\StoreOwnerLeadsEmail($storeOwnerLeads));
    }
    public function sendAppLink($to)
    {
        $customer_type = "";
        $sendAppLink = [
            "username" => $customer_type
        ];
        Mail::to($to)->send(new \App\Mail\SendAppLinkEmail($sendAppLink));
    }
    /**
     * @OA\POST(
     *     path="/api/V1/mailSignupSms/mailSmsPage?action=ForgotPassword",
     *      @OA\Parameter(
     *      name="params",
     *      in="query",
     *      @OA\Schema(
     *      type="object",
     * @OA\Property(property="email", type="string"),
     * @OA\Property(property="name", type="string"),
     * @OA\Property(property="key", type="string")
     * )),
     *     @OA\Response(response=200, description="Success"),
     *     @OA\Response(response=401, description="Unauthenticated"),
     *     @OA\Response(response=403, description="Forbidden"),
     *     @OA\Response(response=404, description="Not Found")
     * )
     */
    public function ForgotPassword()
    {
        $params   =  $this->getData($_REQUEST);
        $customer_name = $params["name"];
        $to = $params["email"];
        $key = $params["key"];
        $pageurl = "https://".$_SERVER['HTTP_HOST']."/reset-password/?Key=".$key."";
        $forgotPassword = [
           "customer_name"=> $customer_name,
           "pageurl"=>$pageurl
        ];
        try {
            Mail::to($to)->send(new \App\Mail\ForgotPasswordEmail($forgotPassword));
        } catch (Response $e) {
            echo 'Caught exception: '.  $e->getMessage(). "\n";
        }
    }
    /**
     * @OA\POST(
     *     path="/api/V1/mailSignupSms/mailSmsPage?action=Membership_warning_email",
     *      @OA\Parameter(
     *      name="params",
     *      in="query",
     *      @OA\Schema(
     *      type="object",
     * @OA\Property(property="email", type="string"),
     * @OA\Property(property="name", type="string"),
     * @OA\Property(property="days", type="integer"),
     * @OA\Property(property="customerType", type="string")
     * )),
     *     @OA\Response(response=200, description="Success"),
     *     @OA\Response(response=401, description="Unauthenticated"),
     *     @OA\Response(response=403, description="Forbidden"),
     *     @OA\Response(response=404, description="Not Found")
     * )
     */
    //Membership expiry warning email
    public function MembershipWarningMail()
    {
        $params   =  $this->getData($_REQUEST);
        $to = $params['email'];
        $userName = $params['name'];
        $membershipName = $params['customerType'];
        $days = $params['days'];
        if ($_SERVER['HTTP_HOST'] == 'localhost'  || $_SERVER['HTTP_HOST'] == '127.0.0.1:8000') {
            $pageUrl = "http://localhost/bemodo_project/bemodo/profile-page/";
        } else {
            $pageUrl = "https://".$_SERVER['HTTP_HOST']."/profile-page/";
        }
        $membershipWarning = [
            "customer_name"=> $userName,
            "membershipName"=>$membershipName,
            "days"=>$days,
            "pageurl"=>$pageUrl
         ];
        try {
            Mail::to($to)->send(new \App\Mail\MembershipWarningEmail($membershipWarning));
        } catch (Response $e) {
            echo 'Caught exception: '.  $e->getMessage(). "\n";
        }
    }
    /**
     * @OA\POST(
     *     path="/api/V1/mailSignupSms/mailSmsPage?action=Membership_expiry_email",
     *     @OA\Response(response=200, description="Success"),
     *     @OA\Response(response=401, description="Unauthenticated"),
     *     @OA\Response(response=403, description="Forbidden"),
     *     @OA\Response(response=404, description="Not Found")
     * )
     */
    //Membership expiry Mail
    public function MembershipExpiryMail()
    {
        $to = "kana123@yopmail.com";
        $username = "testUser";
        $membershipname = "customer";
        $membershipExpiry = [
            "user_name" => $username,
            "membership_name" => $membershipname,
            "email" => $to
        ];
        try {
            Mail::to($to)->send(new \App\Mail\MembershipExpiryEmail($membershipExpiry));
        } catch (Response $e) {
            echo 'Caught exception: '.  $e->getMessage(). "\n";
        }
    
    }
    /**
     * @OA\POST(
     *     path="/api/V1/mailSignupSms/mailSmsPage?action=autorenewal_cancellation",
     *      @OA\Parameter(
     *      name="params",
     *      in="query",
     *      @OA\Schema(
     *      type="object",
     * @OA\Property(property="email", type="string"),
     * @OA\Property(property="userName", type="string"),
     * @OA\Property(property="start_date", type="integer"),
     * @OA\Property(property="renewal_date", type="integer"),
     * @OA\Property(property="membership_id", type="integer"),
     * @OA\Property(property="phoneNumber", type="integer"),
     * @OA\Property(property="customerType", type="string")
     * )),
     *     @OA\Response(response=200, description="Success"),
     *     @OA\Response(response=401, description="Unauthenticated"),
     *     @OA\Response(response=403, description="Forbidden"),
     *     @OA\Response(response=404, description="Not Found")
     * )
     */
    //Auto Renewal cancellation Mail to user
    public function autoRenewalCancellationMail()
    {
        $params   =  $this->getData($_REQUEST);
        $to = $params['email'];
        $username = $params['userName'];
        $mobile = $params['phoneNumber'];
        $membership = $params['customerType'];
        $membership_id = $params['membership_id'];
        $start_date = $params['start_date'];
        $renewal_date = $params['renewal_date'];
        $autoRenewalCancellation = [
            "username" => $username,
            "membership" => $membership
        ];
        try {
            Mail::to($to)->send(new \App\Mail\AutoRenewalCancellationEmail($autoRenewalCancellation));
            $this->autorenewalCancellationSupportTeam($username, $mobile, $membership, $membership_id, $start_date, $renewal_date);
        } catch (Response $e) {
            echo 'Caught exception: '.  $e->getMessage(). "\n";
        }
    }
    //Auto Renewal cancellation Mail to support team
    public function autorenewalCancellationSupportTeam($username, $mobile, $membership, $membership_id, $start_date, $renewal_date)
    {
        $to = "kana123@yopmail.com";
        //$to = "marketing@bemodo.com";
        $membershipExpiry = [
            "username" => $username,
            "membership_id" => $membership_id,
            "membership_type" => $membership,
            "start_date" => $start_date,
            "renewal_date" => $renewal_date
        ];
        try {
            Mail::to($to)->send(new \App\Mail\MembershipExpiryEmail($membershipExpiry));
        } catch (Response $e) {
            echo 'Caught exception: '.  $e->getMessage(). "\n";
        }
    }
    /**
     * @OA\POST(
     *     path="/api/V1/mailSignupSms/mailSmsPage?action=customer_vip",
     *      @OA\Parameter(
     *      name="params",
     *      in="query",
     *      @OA\Schema(
     *      type="object",
     * @OA\Property(property="email", type="string"),
     * @OA\Property(property="userName", type="string"),
     * @OA\Property(property="packageName", type="string"),
     * @OA\Property(property="phone", type="integer"),
     * @OA\Property(property="oldCustomerType", type="string")
     * )),
     *     @OA\Response(response=200, description="Success"),
     *     @OA\Response(response=401, description="Unauthenticated"),
     *     @OA\Response(response=403, description="Forbidden"),
     *     @OA\Response(response=404, description="Not Found")
     * )
     */
    /*customer to vip upgrade*/
    public function customerVip()
    {
        $params   =  $this->getData($_REQUEST);
        $to = $params['email'];
        $name = $params['userName'];
        $oldMembershipType = $params['oldCustomerType'];
        $mobile = $params['phone'];
        $package = $params['packageName'];
        $this->accountsUpgradeMail($to, $name, $oldMembershipType, $package, $mobile);
    }
    /**
     * @OA\POST(
     *     path="/api/V1/mailSignupSms/mailSmsPage?action=customer_storeowner",
     *      @OA\Parameter(
     *      name="params",
     *      in="query",
     *      @OA\Schema(
     *      type="object",
     * @OA\Property(property="email", type="string"),
     * @OA\Property(property="userName", type="string"),
     * @OA\Property(property="packageName", type="string"),
     * @OA\Property(property="phone", type="integer"),
     * @OA\Property(property="oldCustomerType", type="string")
     * )),
     *     @OA\Response(response=200, description="Success"),
     *     @OA\Response(response=401, description="Unauthenticated"),
     *     @OA\Response(response=403, description="Forbidden"),
     *     @OA\Response(response=404, description="Not Found")
     * )
     */
    /*customer to store owner upgrade*/
    public function customerStoreOwner()
    {
        $params   =  $this->getData($_REQUEST);
        $to = $params['email'];
        $affiliateName = $params['userName'];
        $oldMembershipType = $params['oldCustomerType'];
        $mobile = $params['phone'];
        $package = $params['packageName'];
        $this->accountsUpgradeMail($to, $affiliateName, $oldMembershipType, $package, $mobile);
    }
    /**
     * @OA\POST(
     *     path="/api/V1/mailSignupSms/mailSmsPage?action=vip_storeowner",
     *      @OA\Parameter(
     *      name="params",
     *      in="query",
     *      @OA\Schema(
     *      type="object",
     * @OA\Property(property="email", type="string"),
     * @OA\Property(property="userName", type="string"),
     * @OA\Property(property="packageName", type="string"),
     * @OA\Property(property="phone", type="integer"),
     * @OA\Property(property="oldCustomerType", type="string")
     * )),
     *     @OA\Response(response=200, description="Success"),
     *     @OA\Response(response=401, description="Unauthenticated"),
     *     @OA\Response(response=403, description="Forbidden"),
     *     @OA\Response(response=404, description="Not Found")
     * )
     */
    /*vip to store owner upgrade*/
    public function vipStoreOwner()
    {
        $params   =  $this->getData($_REQUEST);
        $to = $params['email'];
        $affiliateName = $params['userName'];
        $oldMembershipType = $params['oldCustomerType'];
        $mobile = $params['phone'];
        $package = $params['packageName'];
        $this->accountsUpgradeMail($to, $affiliateName, $oldMembershipType, $package, $mobile);
    }
    /**
     * @OA\POST(
     *     path="/api/V1/mailSignupSms/mailSmsPage?action=storeowner_storeowner",
     *      @OA\Parameter(
     *      name="params",
     *      in="query",
     *      @OA\Schema(
     *      type="object",
     * @OA\Property(property="email", type="string"),
     * @OA\Property(property="userName", type="string"),
     * @OA\Property(property="packageName", type="string"),
     * @OA\Property(property="phone", type="integer"),
     * @OA\Property(property="oldCustomerType", type="string")
     * )),
     *     @OA\Response(response=200, description="Success"),
     *     @OA\Response(response=401, description="Unauthenticated"),
     *     @OA\Response(response=403, description="Forbidden"),
     *     @OA\Response(response=404, description="Not Found")
     * )
     */
    /*store owner upgrade from level 1 to level2 and level 3*/
    public function storeOwnerStoreOwner()
    {
        $params   =  $this->getData($_REQUEST);
        $to = $params['email'];
        $affiliateName = $params['userName'];
        $oldMembershipType = $params['oldCustomerType'];
        $mobile = $params['phone'];
        $package = $params['packageName'];
        $this->accountsUpgradeMail($to, $affiliateName, $oldMembershipType, $package, $mobile);
    }
    /**
     * @OA\POST(
     *     path="/api/V1/mailSignupSms/mailSmsPage?action=upgrade_to_mallowner",
     *      @OA\Parameter(
     *      name="params",
     *      in="query",
     *      @OA\Schema(
     *      type="object",
     * @OA\Property(property="email", type="string"),
     * @OA\Property(property="userName", type="string"),
     * @OA\Property(property="packageName", type="string"),
     * @OA\Property(property="phone", type="integer"),
     * @OA\Property(property="oldCustomerType", type="string"),
     * @OA\Property(property="couponStatus", type="string"),
     * @OA\Property(property="couponName", type="string")
     * )),
     *     @OA\Response(response=200, description="Success"),
     *     @OA\Response(response=401, description="Unauthenticated"),
     *     @OA\Response(response=403, description="Forbidden"),
     *     @OA\Response(response=404, description="Not Found")
     * )
     */
    /*Customer upgrade from Customer to mallowner*/
    public function upgradeToMallOwner()
    {
        $params   =  $this->getData($_REQUEST);
        $to = $params['email'];
        $affiliateName = $params['userName'];
        $oldMembershipType = $params['oldCustomerType'];
        $mobile = $params['phone'];
        $package = $params['packageName'];
        $couponStatus = $params["couponStatus"];
        $couponName = $params["couponName"];
        $this->accountsUpgradeMail($to, $affiliateName, $oldMembershipType, $package, $mobile, $couponStatus, $couponName);
    }
    public function accountsUpgradeMail($to, $username, $oldMembershipType, $package, $mobile, $couponstatus="", $couponName="")
    {
        if ($package == "MVP") {
            $customer_type = "Lifestyle Customer -1 Month";
            $start_date = date("m/d/Y");
            $renewal_date = date('m/d/Y', strtotime($start_date. '1 month'));
        }
        if ($package == "MVA") {
            $customer_type = "Lifestyle Customer -12 Month";
            $start_date = date("m/d/Y");
            $renewal_date = date('m/d/Y', strtotime($start_date. '12 month'));
        } elseif ($package == "SSO" || $package == "SSOVIP") {
            $customer_type = "Starter Store Owner";
            $start_date = date("m/d/Y");
            $renewal_date = "N/A";
        } elseif ($package == "ASOM") {
            $customer_type = "Advanced Store Owner - 1 Month";
            $start_date = date("m/d/Y");
            $renewal_date = date('m/d/Y', strtotime($start_date. '1 month'));
        } elseif ($package == "PSOM") {
            $customer_type = "Pro Store Owner - 1 Month";
            $start_date = date("m/d/Y");
            $renewal_date = date('m/d/Y', strtotime($start_date. '1 month'));
        } elseif ($package == "ASOMY") {
            $customer_type = "Advanced Store Owner - 1 Year";
            $start_date = date("m/d/Y");
            $renewal_date = date('m/d/Y', strtotime($start_date. '12 month'));
        } elseif ($package == "PSOMY") {
            $customer_type = "Pro Store Owner - 1 Year";
            $start_date = date("m/d/Y");
            $renewal_date = date('m/d/Y', strtotime($start_date. '12 month'));
        } elseif ($package == "MMO") {
            if ($couponstatus == "yes" && $couponName == "BEMMALLNEW") {
                $customer_type = "Mall Owner - 2 Month";
                $start_date = date("m/d/Y");
                $renewal_date = date('m/d/Y', strtotime($start_date. '2 month'));
            } elseif ($couponstatus == "yes" && $couponName == "BEMMALLSET") {
                $customer_type = "Mall Owner - 1 Month";
                $start_date = date("m/d/Y");
                $renewal_date = date('m/d/Y', strtotime($start_date. '1 month'));
            } elseif ($couponstatus == "yes" && $couponName == "BEMMALLFREE") {
                $customer_type = "Mall Owner - 2 Month";
                $start_date = date("m/d/Y");
                $renewal_date = date('m/d/Y', strtotime($start_date. '2 month'));
            } elseif ($couponstatus == "yes" && $couponName == "MallFndr22") {
                $customer_type = "Mall Owner - 1 Month";
                $start_date = date("m/d/Y");
                $renewal_date = date('m/d/Y', strtotime($start_date. '1 month'));
            } else {
                $customer_type = "Mall Owner - 1 Month";
                $start_date = date("m/d/Y");
                $renewal_date = date('m/d/Y', strtotime($start_date. '1 month'));
            }
        }  elseif ($package == "SMO") {
            if ($couponstatus == "yes") {
                $customer_type = "Mall Owner";
                $start_date = date("m/d/Y");
                $renewal_date = date('m/d/Y', strtotime($start_date. '2 month'));
            } else {
                $customer_type = "Mall Owner - 1 Year";
                $start_date = date("m/d/Y");
                $renewal_date = date('m/d/Y', strtotime($start_date. '12 month'));
            }
        }
        if ($_SERVER['HTTP_HOST'] == 'localhost' || $_SERVER['HTTP_HOST'] == '127.0.0.1:8000') {
            $pageurl = "http://localhost/bemodo_project/bemodo/login-page/";
        } else {
            $pageurl = "https://".$_SERVER['HTTP_HOST']."/login-page/";
        }
        $accountsUpgradeMail = [
            "user_name" => $username,
            "membership_name" => $oldMembershipType,
            "email" => $to,
            "old_membership_type" => $oldMembershipType,
            "new_membership_type" => $customer_type,
            "start_date" => $start_date,
            "renewal_date" => $renewal_date,
            "pageurl" =>$pageurl
        ];
        try {
            Mail::to($to)->send(new \App\Mail\AccountsUpgradeEmail($accountsUpgradeMail));
            $this->accountsUpgradeEmailSupport($username, $customer_type, $oldMembershipType, $start_date, $renewal_date);
            $this->accountsUpgradeSms($username, $mobile, $customer_type, $start_date, $renewal_date);
        } catch (Response $e) {
            echo 'Caught exception: '.  $e->getMessage(). "\n";
        }
    }
    public function accountsUpgradeEmailSupport($username, $customer_type, $oldMembershipType, $start_date, $renewal_date)
    {
        $to = "kana123@yopmail.com";
        //$to = "marketing@bemodo.com";
        $accountsUpgradeSupport = [
            "user_name" => $username,
            "membership_name" => $oldMembershipType,
            "email" => $to,
            "old_membership_type" => $oldMembershipType,
            "new_membership_type" => $customer_type,
            "start_date" => $start_date,
            "renewal_date" => $renewal_date
        ];
        try {
            Mail::to($to)->send(new \App\Mail\AccountsUpgradeSupportEmail($accountsUpgradeSupport));
        } catch (Response $e) {
            echo 'Caught exception: '.  $e->getMessage(). "\n";
        }
    }
    public function accountsUpgradeSms($userName, $mobile, $customerType, $startDate, $renewalDate)
    {
        $content = "Hello $userName, You are successfully upgraded as $customerType. Your membership details are mentioned below : Start date : $startDate Renewal date : $renewalDate";
        if ($_SERVER['HTTP_HOST'] == 'localhost' || $_SERVER['HTTP_HOST'] == 'bemodonew.wpengine.com' || $_SERVER['HTTP_HOST'] == '127.0.0.1:8000') {
            $mobileNo =    '+91'.$mobile;
        } else {
            $mobileNo =    '+1'.$mobile;
        }

        try {
  
            $account_sid = getenv("TWILIO_SID");
            $auth_token = getenv("TWILIO_TOKEN");
            $twilio_number = getenv("TWILIO_FROM");
  
            $client = new Client($account_sid, $auth_token);
            $client->messages->create($mobileNo, [
                'from' => $twilio_number,
                'body' => $content]);
  
            dd('SMS Sent Successfully.');
  
        } catch (Response $e) {
            dd("Error: ". $e->getMessage());
        }
    }
    /**
     * @OA\POST(
     *     path="/api/V1/mailSignupSms/mailSmsPage?action=account_cancellation_email",
     *      @OA\Parameter(
     *      name="params",
     *      in="query",
     *      @OA\Schema(
     *      type="object",
     * @OA\Property(property="email", type="string"),
     * @OA\Property(property="username", type="string"),
     * @OA\Property(property="membership_id", type="integer"),
     * @OA\Property(property="mobile", type="integer"),
     * @OA\Property(property="membership_type", type="string"),
     * @OA\Property(property="start_date", type="integer"),
     * @OA\Property(property="renewal_date", type="integer")
     * )),
     *     @OA\Response(response=200, description="Success"),
     *     @OA\Response(response=401, description="Unauthenticated"),
     *     @OA\Response(response=403, description="Forbidden"),
     *     @OA\Response(response=404, description="Not Found")
     * )
     */
    public function accountCancellation()
    {
        $params   =  $this->getData($_REQUEST);
        $username = $params['username'];
        $to = $params['email'];
        $mobile = $params['mobile'];
        $membershipType = $params['membership_type'];
        $membershipId = $params['membership_id'];
        $start_date = $params['start_date'];
        $startDate = date('m/d/Y', strtotime($start_date));
        $renewal_date = $params['renewal_date'];
        $renewalDate = date('m/d/Y', strtotime($renewal_date));
        $accountsCancellation = [
            "user_name" => $username
        ];
        try {
            Mail::to($to)->send(new \App\Mail\AccountCancellationEmail($accountsCancellation));
            $this->accountCancellationSupportTeamEmail($username, $membershipId, $membershipType, $startDate, $renewalDate);
            $this->accountCancellationSms($mobile, $username, $membershipType);
        } catch (Response $e) {
            echo 'Caught exception: '.  $e->getMessage(). "\n";
        }
    }
    public function accountCancellationSupportTeamEmail($username, $membership_id, $membership_type, $startdate, $renewaldate)
    {
        $to = "kana123@yopmail.com";
        //$to = "marketing@bemodo.com";
        $accountsCancellationSupport = [
            "username" => $username,
            "membership_id" => $membership_id,
            "membership_type" => $membership_type,
            "start_date" => $startdate,
            "renewal_date" => $renewaldate
        ];
        try {
            Mail::to($to)->send(new \App\Mail\AccountsCancellationSupportEmail($accountsCancellationSupport));
        } catch (Response $e) {
            echo 'Caught exception: '.  $e->getMessage(). "\n";
        }
    }
    public function accountCancellationSms($mobile, $userName, $membershipType)
    {
        $content = "Hello $userName, We regret to inform you that your $membershipType with Bemodo has been cancelled due to non payment of membership fee. Please contact Support for further queries and assistance";
        if ($_SERVER['HTTP_HOST'] == 'localhost' || $_SERVER['HTTP_HOST'] == 'bemodonew.wpengine.com' || $_SERVER['HTTP_HOST'] == '127.0.0.1:8000') {
            $mobileNo =    '+91'.$mobile;
        } else {
            $mobileNo =    '+1'.$mobile;
        }

        try {
  
            $account_sid = getenv("TWILIO_SID");
            $auth_token = getenv("TWILIO_TOKEN");
            $twilio_number = getenv("TWILIO_FROM");
  
            $client = new Client($account_sid, $auth_token);
            $client->messages->create($mobileNo, [
                'from' => $twilio_number,
                'body' => $content]);
  
            dd('SMS Sent Successfully.');
  
        } catch (Response $e) {
            dd("Error: ". $e->getMessage());
        }
    }
    /**
     * @OA\POST(
     *     path="/api/V1/mailSignupSms/mailSmsPage?action=account_downgrade",
     *      @OA\Parameter(
     *      name="params",
     *      in="query",
     *      @OA\Schema(
     *      type="object",
     * @OA\Property(property="email", type="string"),
     * @OA\Property(property="FirstName", type="string"),
     * @OA\Property(property="LastName", type="string"),
     * @OA\Property(property="addonType", type="string"),
     * @OA\Property(property="mobile", type="integer"),
     * @OA\Property(property="membership_type", type="string"),
     * @OA\Property(property="start_date", type="integer"),
     * @OA\Property(property="renewal_date", type="integer")
     * )),
     *     @OA\Response(response=200, description="Success"),
     *     @OA\Response(response=401, description="Unauthenticated"),
     *     @OA\Response(response=403, description="Forbidden"),
     *     @OA\Response(response=404, description="Not Found")
     * )
     */
    //Account downgrade Email to user
    public function accountDowngrade()
    {
        $params   =  $this->getData($_REQUEST);
        $userName = $params['FirstName']." ".$params['LastName'];
        $emailId = $params['email'];
        $mobile = $params['mobile'];
        $membershipType = $params['membership_type'];
        $startDate = $params['start_date'];
        $renewalDate = $params['renewal_date'];
        $addonType = $params['addonType'];
        if ($membershipType == "VIP Customer") {

            $downgradeLevel = "FREE CUSTOMER";
        } elseif ($addonType != "" && ($membershipType == "Standard Mall Owner" || $membershipType == "Mall Owner")) {
            $downgradeLevel = $addonType;
        } else {
            $downgradeLevel = "Starter Store Owner";
        }
        $accountDowngrade = [
            "user_name" => $userName,
            "downgradelevel" => $downgradeLevel
        ];
        try {
            Mail::to($emailId)->send(new \App\Mail\AccountDowngradeEmail($accountDowngrade));
            $this->accountDowngradeSupportTeamMail($userName, $membershipType, $startDate, $renewalDate);
            $this->accountDowngradeSms($mobile, $userName, $downgradeLevel);
        } catch (Response $e) {
            echo 'Caught exception: '.  $e->getMessage(). "\n";
        }
    }
    //Account downgrade Email to support team
    public function accountDowngradeSupportTeamMail($userName, $membershipType, $startDate, $renewalDate)
    {
        $to = "kana123@yopmail.com";
        //$to = "marketing@bemodo.com";
        $accountDowngradeSupport = [
            "username" => $userName,
            "membership_type" => $membershipType,
            "start_date" => $startDate,
            "renewal_date" => $renewalDate
        ];
        try {
            Mail::to($to)->send(new \App\Mail\AccountDowngradeSupportEmail($accountDowngradeSupport));
        } catch (Response $e) {
            echo 'Caught exception: '.  $e->getMessage(). "\n";
        }
    }

    public function accountDowngradeSms($mobile, $userName, $downgradeLevel)
    {
        $content = "Hi $userName,We regret to inform you that your account has been downgraded to $downgradeLevel on account of failure to renew.";
        if ($_SERVER['HTTP_HOST'] == 'localhost' || $_SERVER['HTTP_HOST'] == 'bemodonew.wpengine.com' || $_SERVER['HTTP_HOST'] == '127.0.0.1:8000') {
            $mobileNo =    '+91'.$mobile;
        } else {
            $mobileNo =    '+1'.$mobile;
        }

        try {
  
            $account_sid = getenv("TWILIO_SID");
            $auth_token = getenv("TWILIO_TOKEN");
            $twilio_number = getenv("TWILIO_FROM");
  
            $client = new Client($account_sid, $auth_token);
            $client->messages->create($mobileNo, [
                'from' => $twilio_number,
                'body' => $content]);
  
            dd('SMS Sent Successfully.');
  
        } catch (Response $e) {
            dd("Error: ". $e->getMessage());
        }
    }
    /**
     * @OA\POST(
     *     path="/api/V1/mailSignupSms/mailSmsPage?action=vendor_query",
     *      @OA\Parameter(
     *      name="params",
     *      in="query",
     *      @OA\Schema(
     *      type="object",
     * @OA\Property(property="companyname", type="string"),
     * @OA\Property(property="contactname", type="string"),
     * @OA\Property(property="companyURL", type="string"),
     * @OA\Property(property="contactemail", type="string"),
     * @OA\Property(property="contactphone", type="integer"),
     * @OA\Property(property="companydescription", type="string"),
     * @OA\Property(property="productdescription", type="string")
     * )),
     *     @OA\Response(response=200, description="Success"),
     *     @OA\Response(response=401, description="Unauthenticated"),
     *     @OA\Response(response=403, description="Forbidden"),
     *     @OA\Response(response=404, description="Not Found")
     * )
     */
    public function vendorQuery()
    {
        $params   =  $this->getData($_REQUEST);
        $companyName = $params['companyname'];
        $contactName = $params['contactname'];
        $companyURL = $params['companyURL'];
        $contactEmail = $params['contactemail'];
        $contactPhone = $params['contactphone'];
        $companyDescription = $params['companydescription'];
        $productDescription = $params['productdescription'];
        //$to = "support@bemodo.com";
        $to = "kana123@yopmail.com";
        $vendor = [
            "companyname" => $companyName,
            "contactname" => $contactName,
            "companyURL" => $companyURL,
            "contactemail" => $contactEmail,
            "contactphone" => $contactPhone,
            "companydescription" => $companyDescription,
            "productdescription" => $productDescription
        ];
        try {
            Mail::to($to)->send(new \App\Mail\VendorQueryEmail($vendor));
        } catch (Response $e) {
            echo 'Caught exception: '.  $e->getMessage(). "\n";
        }
    }
    /**
     * @OA\POST(
     *     path="/api/V1/mailSignupSms/mailSmsPage?action=membership_grace_period",
     *      @OA\Parameter(
     *      name="params",
     *      in="query",
     *      @OA\Schema(
     *      type="object",
     * @OA\Property(property="email", type="string"),
     * @OA\Property(property="name", type="string"),
     * @OA\Property(property="customerType", type="string")
     * )),
     *     @OA\Response(response=200, description="Success"),
     *     @OA\Response(response=401, description="Unauthenticated"),
     *     @OA\Response(response=403, description="Forbidden"),
     *     @OA\Response(response=404, description="Not Found")
     * )
     */
    public function membershipGracePeriod()
    {
        $params   =  $this->getData($_REQUEST);
        $to = $params['email'];
        $userName = $params['name'];
        $membershipName = $params['customerType'];
        if ($_SERVER['HTTP_HOST'] == 'localhost' || $_SERVER['HTTP_HOST'] == '127.0.0.1:8000') {
            $pageUrl = "http://localhost/bemodo_project/bemodo/profile-page/";
        } else {
            $pageUrl = "https://".$_SERVER['HTTP_HOST']."/profile-page/";
        }
        $membershipPeriod = [
            "user_name" => $userName,
            "membership_name" => $membershipName,
            "email" => $to,
            "pageurl" => $pageUrl
        ];
        try {
            Mail::to($to)->send(new \App\Mail\MembershipGracePeriodEmail($membershipPeriod));
        } catch (Response $e) {
            echo 'Caught exception: '.  $e->getMessage(). "\n";
        }
    }
}
