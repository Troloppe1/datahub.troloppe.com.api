<?php

namespace App\Enums;

enum UserRolesEnum: string {
    case ADMIN = 'Admin';
    case RESEARCH_MANAGER = 'Research Manager';
    case RESEARCH_STAFF = 'Research Staff'; 
}