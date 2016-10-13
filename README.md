supervisor-control
==================

ZF3 Module for supervisord management based on mondalaci/supervisor-client.
Offers a frontend that allows to restart the supervisord daemon and start/stop
single processes, all processes of a group or all configured processes. Shows
information about the configured programm groups and the running processes.

Configuration
-------------

Add to config/autoload/supervisorcontrol.local.php:
```php
    'supervisor_client' => [
        'hostname' => 'unix:///var/lib/supervisord/supervisor.sock',
        'port'     => -1,
    ],
```

or:
```php
    'supervisor_client' => [
        'hostname' => '127.0.0.1',
        'port'     => 9001,
    ],
```

Usage
-----

Entry route is /supervisor-control

Notice: To use supervisor.getProcessInfo() with a program group that uses
numprocs > 1 the full qualified name (FQN, "group:process") of a process
is required:
```
[program:myprog]
numprocs     = 2
process_name = myprog-%(process_num)d
````
This will produce process names like myprog-0, myprog-1. When calling
getProcessInfo("myprog-0") an exception with BAD_NAME will be thrown,
use getProcessInfo("myprog:myprog-0") instead.
This module overrides getProcessInfo to construct the FQN automatically before
querying the API. The extended client functions getProcessConfig, processExists,
getProcessState and isProcessRunning work when either the short name or FQN is
given.

Todo
----

Adjust route constraints to allow all possible group/process names,
[a-zA-Z0-9_-]+ is probably to restrictive.

Implement twiddler support for modifying and reloading the configuration.