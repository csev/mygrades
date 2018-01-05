<?php

// The SQL to uninstall this tool
$DATABASE_UNINSTALL = array(
"drop table if exists {$CFG->dbprefix}mygradesActivities"
);

// The SQL to create the tables if they don't exist
$DATABASE_INSTALL = array(
array( "{$CFG->dbprefix}mygradesActivities",
"create table {$CFG->dbprefix}mygradesActivities (
    context     INTEGER NOT NULL,
    activity_id     		INT NOT NULL AUTO_INCREMENT,
    definition  MEDIUMTEXT,

    CONSTRAINT `{$CFG->dbprefix}mygradesActivities_ibfk_1`
        FOREIGN KEY (`context`)
        REFERENCES `{$CFG->dbprefix}lti_context` (`context_id`)
        ON DELETE CASCADE ON UPDATE CASCADE,
		UNIQUE(activity_id)

    
) ENGINE = InnoDB DEFAULT CHARSET=utf8"),
array( "{$CFG->dbprefix}mygradesStatements",
"create table {$CFG->dbprefix}mygradesStatements (
    statement_id     INT NOT NULL AUTO_INCREMENT,
	activity		INT,
    agent     		TEXT,
	grade			FLOAT,

    CONSTRAINT `{$CFG->dbprefix}mygradesStatements_ibfk_1`
        FOREIGN KEY (`activity`)
        REFERENCES `{$CFG->dbprefix}mygradesActivities` (`activity_id`)
        ON DELETE CASCADE ON UPDATE CASCADE,
		UNIQUE(statement_id)

    
) ENGINE = InnoDB DEFAULT CHARSET=utf8")
);

