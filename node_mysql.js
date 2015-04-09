var mysql = require('mysql');
var connection = mysql.createConnection({
    host: 'localhost',
    user: 'root',
    password: ''
});

connection.connect(function(err) {
    if (err) {
        console.log('Erreur sur la connexion: ' + err.strack);
        return;
    }
    console.log('Connecté en Id : ' + connection.threadId);

});

connection.query('SELECT 1 + 1 AS solution', function(err, rows, fields) {
    if (err) throw err;
    // console.log('La solution est : ', rows[0].solution);
    console.log('La solution est : '     +  JSON.stringify(rows) );
});

connection.end();