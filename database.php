<?php

// The SQL to uninstall this tool
$DATABASE_UNINSTALL = array(
"drop table if exists {$CFG->dbprefix}mygradesActivities"
);

// The SQL to create the tables if they don't exist
$DATABASE_INSTALL = array(
array( "{$CFG->dbprefix}mygradesActivities",
"create table {$CFG->dbprefix}mygradesActivities (
    context_id     INTEGER NOT NULL,
    id     		VARCHAR(256),
	id_sha256	CHAR(64),
    definition  MEDIUMTEXT,

    CONSTRAINT `{$CFG->dbprefix}mygradesActivities_ibfk_1`
        FOREIGN KEY (`context_id`)
        REFERENCES `{$CFG->dbprefix}lti_context` (`context_id`)
        ON DELETE CASCADE ON UPDATE CASCADE,
		UNIQUE(id_sha256)

    
) ENGINE = InnoDB DEFAULT CHARSET=utf8")
);

