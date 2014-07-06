supervisor-control
==================

ZF2 Module for supervisord management based on mondalaci/supervisor-client.
Offers a frontend that allows to restart the supervisord daemon and start/stop
single processes, all processes of a group or all configured processes. Shows
information about the configured programm groups and the running processes.

Configuration
-------------

Add to config/autoload/supervisorcontrol.local.php:
```php
    'supervisor_client' => array(
        'hostname' => 'unix:///var/lib/supervisord/supervisor.sock',
        'port'     => -1,
    ),
```

or:
```php
    'supervisor_client' => array(
        'hostname' => '127.0.0.1',
        'port'     => 9001,
    ),
```

Usage
-----

Entry route is /supervisor-control

Todo
----

When a program group has "numprocs > 1" and "process_name = myprog-%(process_num)"
set supervisor appends "-00", "-01" and so on to the process name.
These names show up in the result of supervisor.getAllConfigInfo and
supervisor.getAllProcessInfo but cannot be accessed by any process specific
function, e.g. supervisor.getProcessInfo, it fails with BAD_NAME
-> https://github.com/Supervisor/supervisor/issues/454

Adjust route constraints to allow all possible group/process names,
[a-zA-Z0-9_-]+ is probably to restrictive.

Implement twiddler support for modifying and reloading the configuration