<?php
namespace UMC\consts;

abstract class FileStatus {
    
    const unknown = 'UNK';
    const in_database = 'IDB';
    const in_server = 'ISE';
    const asking = 'ASK';
    const used = 'USE';
    const not_used = 'NUS';
    const erasing = 'ERA';
    const deleted = 'DEL';
    const making_backup = 'MBA';
    const backup_made = 'MBA';
    const error_backup = 'EBA';
    const error = 'ERR';
    const error_delete = 'EDE';
}