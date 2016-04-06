[__USERNAME__]
listen = __SOCKET__
listen.owner = __USERNAME__
listen.group = apache
listen.mode = 0660
user = __USERNAME__
group = __USERNAME__
catch_workers_output = no
pm = ondemand
pm.max_children = __MAXCHILDREN__
pm.max_requests = 10000
pm.process_idle_timeout = 20s
php_admin_value[error_log] = __ERRORLOG__
