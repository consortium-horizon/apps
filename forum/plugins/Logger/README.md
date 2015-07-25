#Logger Plugin for Vanilla 2.0/2.1

##Description
This plugins implements a Logger in Vanilla. It is based on [Log4php](http://logging.apache.org/log4php/) and it offers great flexibility.

##Supported appenders
The plugin supports [all appenders provided by Log4php](http://logging.apache.org/log4php/docs/appenders.html) and adds a couple more, for your convenience, listed below.

###LoggerAppenderVanillaDB
This appender writes the log to the database used by your Vanilla instance.

#### Appender parameters
* **TableName**: the table where the log messages will be stored.
* **CreateTable**: indicates if the destination table should be created, if it doesn't exist.

If you wish to create the table manually, please use the following SQL command:

~~~
CREATE TABLE <TABLE_NAME> (
	`LogEntryID` int(11) NOT NULL AUTO_INCREMENT,
	`TimeStamp` datetime NOT NULL,  
	`LoggerName` varchar(100) COLLATE utf8_unicode_ci NOT NULL,  
	`Level` varchar(40) COLLATE utf8_unicode_ci NOT NULL,  
	`Message` text COLLATE utf8_unicode_ci NOT NULL,  
	`Thread` varchar(32) COLLATE utf8_unicode_ci NOT NULL,  
	`ClassName` varchar(100) COLLATE utf8_unicode_ci NOT NULL,  
	`MethodName` varchar(200) COLLATE utf8_unicode_ci NOT NULL,  
	`FileName` varchar(400) COLLATE utf8_unicode_ci NOT NULL,  
	`LineNumber` int(11) NOT NULL,  
	`Exception` text COLLATE utf8_unicode_ci,  
	`InsertUserID` int(11) DEFAULT NULL,  
PRIMARY KEY (`LogEntryID`),
KEY `IX_LoggerSysLog_TimeStamp` (`TimeStamp`)) DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci
~~~

###LoggerAppenderGraylog2
This appender allows you to post messages to a [Graylog2 server](http://graylog2.org/).

#### Appender parameters
* **HostName**: the name or IP address of Graylog2 server.
* **Port**: the port to use to communicate with Graylog2.
* **Chunksize**: the size of the chunks to send to Graylog2.

###LoggerAppenderLoggly
This appender sends the messages to [Loggly, a cloud based log management service](https://www.loggly.com/), using HTTPS protocol. **Important**: HTTPS is quite slow, therefore it's not recommended to use this appender extensively.

#### Appender parameters
* **InputKey**: the SHA Input Key to be used to send Logs to Loggly via HTTPS

###LoggerAppenderLogglySyslog
This appender sends the messages to [Loggly, a cloud based log management service](https://www.loggly.com/), using Remote SysLog protocol. Data is sent in JSON format, to simplify analysis. This logger is preferable to the HTTPS one for common usage, as it's much faster.

#### Appender parameters
* **HostName**: the name or IP address of RSysLog server.
* **Port**: the port to use to communicate with the RSysLog server.
* **Timeout**: the timeout to use when communicating with the RSysLog server.

##LoggerAppenderRSyslog
A generic logger to send data to Remote SysLog servers. It can be used to send data to [PaperTrail](https://papertrailapp.com/).

#### Appender parameters
* **HostName**: the name or IP address of RSysLog server.
* **Port**: the port to use to communicate with the RSysLog server.
* **Timeout**: the timeout to use when communicating with the RSysLog server.


##Installation
Follow standard plugin installation procedure.

##Default configuration
Logger plugin has been designed to work out of the box. In its default configuration, it logs all messages to `LoggerSysLog` table in the same database used by your instance of Vanilla Forums. Such table is created the first time a message is logged.

The default Log Level is **INFO**. This means that log messages with a lower level will be ignored. To change it, modify the `config.xml` file by adding the line `<level value="{new_log_level}" />` in the `<root>` node, replacing `{new_log_level}` with your desired Log Level.

To view the allowed values of the Log Level, please visit [Log4php website](http://logging.apache.org/log4php/docs/configuration.html).

##Changing the configuration
If you wish to change the configuration, you have to modify file `config.xml`, which can be found in plugin's directory. Information on how to write a configuration file can be found on [Log4php website](http://logging.apache.org/log4php/docs/configuration.html).

##Usage
This plugin has been designed to be used by other plugins, or even by Vanilla Core libraries. Using it is straightforward (see the example below):

###Example - Using the Logger

~~~
// Get the Logger instance
$Logger = LoggerPlugin::GetLogger();

// Log several messages, one for each available level
$Logger->trace('This is a TRACE message');
$Logger->debug('This is a DEBUG message');
$Logger->info('This is an INFO message');
$Logger->warn('This is an WARNING message');
$Logger->error('This is an ERROR message');
$Logger->fatal('This is an FATAL message');
~~~
