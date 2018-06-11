var createError = require('http-errors');
var express = require('express');
var path = require('path');
var cookieParser = require('cookie-parser');
var logger = require('morgan');

var indexRouter = require('./routes/index');
var usersRouter = require('./routes/users');

var app = express();

var mysql = require("mysql");

var WooCommerceAPI = require('woocommerce-api'); 

//Database connection
app.use(function(req, res, next){
	global.connection = mysql.createConnection({
		host     : '143.255.143.94',
		port     : '7123',
		user     : 'sitekingdom',
		password : 'KingSite18!',
		database : 'kingdom'
	});
	connection.connect();
	next();
});


var WooCommerce = new WooCommerceAPI({
  url: 'http://localhost/kingdom',
  consumerKey: 'ck_dd36f37480a57074a5b1745c3f5f2ef2e63ac965',
  consumerSecret: 'cs_12f3ec9ea252f4e50cf1e7f5633db3ea9a6d99fd',
  version: 'v3'
});

WooCommerce.getAsync('produto').then(function(result) {
  return JSON.parse(result.toJSON().body);
});

// view engine setup
app.set('views', path.join(__dirname, 'views'));
app.set('view engine', 'pug');

app.use(logger('dev'));
app.use(express.json());
app.use(express.urlencoded({ extended: false }));
app.use(cookieParser());
app.use(express.static(path.join(__dirname, 'public')));

app.use('/', indexRouter);
app.use('/api/v1/users', usersRouter);

// catch 404 and forward to error handler
app.use(function(req, res, next) {
  next(createError(404));
});

// error handler
app.use(function(err, req, res, next) {
  // set locals, only providing error in development
  res.locals.message = err.message;
  res.locals.error = req.app.get('env') === 'development' ? err : {};

  // render the error page
  res.status(err.status || 500);
  res.render('error');
});

module.exports = app;
