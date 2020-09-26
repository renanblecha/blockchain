pragma solidity ^0.6.12;

contract Presence {
    
    struct Student {
		string name;
        bool presence;
    }
    
    address public manager;
	
    mapping(address => Student) public course;
    
    constructor() public {
        manager = msg.sender;
    }
    
	// enroll students in the course
    function enrollStudent(address student, string name) public {
        if(msg.sender != manager) return;
        course[student].name = name;
        course[student].presence = false;
    }
    
	// records the student's presence in the course
    function presenceStudent(address student) public {
        if(msg.sender != manager) return;
        course[student].presence = true;
    }
    
}

Linguagem de preferência: PHP
Biblioteca pra trabalhar com Smart Contracts: ethereum-php
Github: https://github.com/digitaldonkey/ethereum-php
Site: https://ethereum-php.org
