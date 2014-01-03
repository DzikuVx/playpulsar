<?php

namespace Gameplay\Model;

class UserEntity extends Standard {
	protected $tableName = "users";
	protected $tableID = "UserID";
	protected $tableUseFields = array("UserID", "NPCTypeID", "Type", "Login", "AllowSpam", "Password", "Email", "Name", "UserLocked", "UserActivated", "Country", "Language", "About", "FacebookID" );

    public $UserID;
    public $NPCTypeID;
    public $Type;
    public $Login;
    public $AllowSpam;
    public $Password;
    public $Email;
    public $Name;
    public $UserLocked;
    public $UserActivated;
    public $Country;
    public $Language;
    public $About;
    public $FacebookID;
}