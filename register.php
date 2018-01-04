<?php

$REGISTER_LTI2 = array(
"name" => "Grade Book",
"FontAwesome" => "fa-area-chart",
"short_name" => "Grade Book",
"description" => "This tool allows both the students and instructors to view their grades for
a course. Instructors can upload files with grades  
This tool also provides simple grade review and maintenance tools for instructors.",
"privacy_level" => "public",  // anonymous, name_only, public
    "messages" => array("launch"),
    "license" => "Apache",
    "languages" => array(
        "English", "German"
    ),
    "source_url" => "http://services.vcrp.de:9321/dahn/MyGrade",
    // For now 
    "placements" => array(
        "course_navigation"  // Would be nice if this happenned :)
        /*
        "course_navigation", "homework_submission",
        "course_home_submission", "editor_button",
        "link_selection", "migration_selection", "resource_selection",
        "tool_configuration", "user_navigation"
        */
    ),

);

