<?php

// autoload_classmap.php @generated by Composer

$vendorDir = dirname(dirname(__FILE__));
$baseDir = dirname($vendorDir);

return array(
    'Latte\\CompileException' => $vendorDir . '/latte/latte/src/Latte/exceptions.php',
    'Latte\\Compiler' => $vendorDir . '/latte/latte/src/Latte/Compiler/Compiler.php',
    'Latte\\Engine' => $vendorDir . '/latte/latte/src/Latte/Engine.php',
    'Latte\\Helpers' => $vendorDir . '/latte/latte/src/Latte/Helpers.php',
    'Latte\\HtmlNode' => $vendorDir . '/latte/latte/src/Latte/Compiler/HtmlNode.php',
    'Latte\\ILoader' => $vendorDir . '/latte/latte/src/Latte/ILoader.php',
    'Latte\\IMacro' => $vendorDir . '/latte/latte/src/Latte/IMacro.php',
    'Latte\\Loaders\\FileLoader' => $vendorDir . '/latte/latte/src/Latte/Loaders/FileLoader.php',
    'Latte\\Loaders\\StringLoader' => $vendorDir . '/latte/latte/src/Latte/Loaders/StringLoader.php',
    'Latte\\MacroNode' => $vendorDir . '/latte/latte/src/Latte/Compiler/MacroNode.php',
    'Latte\\MacroTokens' => $vendorDir . '/latte/latte/src/Latte/Compiler/MacroTokens.php',
    'Latte\\Macros\\BlockMacros' => $vendorDir . '/latte/latte/src/Latte/Macros/BlockMacros.php',
    'Latte\\Macros\\CoreMacros' => $vendorDir . '/latte/latte/src/Latte/Macros/CoreMacros.php',
    'Latte\\Macros\\MacroSet' => $vendorDir . '/latte/latte/src/Latte/Macros/MacroSet.php',
    'Latte\\Parser' => $vendorDir . '/latte/latte/src/Latte/Compiler/Parser.php',
    'Latte\\PhpHelpers' => $vendorDir . '/latte/latte/src/Latte/Compiler/PhpHelpers.php',
    'Latte\\PhpWriter' => $vendorDir . '/latte/latte/src/Latte/Compiler/PhpWriter.php',
    'Latte\\RegexpException' => $vendorDir . '/latte/latte/src/Latte/exceptions.php',
    'Latte\\RuntimeException' => $vendorDir . '/latte/latte/src/Latte/exceptions.php',
    'Latte\\Runtime\\CachingIterator' => $vendorDir . '/latte/latte/src/Latte/Runtime/CachingIterator.php',
    'Latte\\Runtime\\FilterExecutor' => $vendorDir . '/latte/latte/src/Latte/Runtime/FilterExecutor.php',
    'Latte\\Runtime\\FilterInfo' => $vendorDir . '/latte/latte/src/Latte/Runtime/FilterInfo.php',
    'Latte\\Runtime\\Filters' => $vendorDir . '/latte/latte/src/Latte/Runtime/Filters.php',
    'Latte\\Runtime\\Html' => $vendorDir . '/latte/latte/src/Latte/Runtime/Html.php',
    'Latte\\Runtime\\IHtmlString' => $vendorDir . '/latte/latte/src/Latte/Runtime/IHtmlString.php',
    'Latte\\Runtime\\ISnippetBridge' => $vendorDir . '/latte/latte/src/Latte/Runtime/ISnippetBridge.php',
    'Latte\\Runtime\\SnippetDriver' => $vendorDir . '/latte/latte/src/Latte/Runtime/SnippetDriver.php',
    'Latte\\Runtime\\Template' => $vendorDir . '/latte/latte/src/Latte/Runtime/Template.php',
    'Latte\\Strict' => $vendorDir . '/latte/latte/src/Latte/Strict.php',
    'Latte\\Token' => $vendorDir . '/latte/latte/src/Latte/Compiler/Token.php',
    'Latte\\TokenIterator' => $vendorDir . '/latte/latte/src/Latte/Compiler/TokenIterator.php',
    'Latte\\Tokenizer' => $vendorDir . '/latte/latte/src/Latte/Compiler/Tokenizer.php',
    'Nette\\ArgumentOutOfRangeException' => $vendorDir . '/nette/utils/src/Utils/exceptions.php',
    'Nette\\Bridges\\CacheDI\\CacheExtension' => $vendorDir . '/nette/caching/src/Bridges/CacheDI/CacheExtension.php',
    'Nette\\Bridges\\CacheLatte\\CacheMacro' => $vendorDir . '/nette/caching/src/Bridges/CacheLatte/CacheMacro.php',
    'Nette\\Bridges\\DITracy\\ContainerPanel' => $vendorDir . '/nette/di/src/Bridges/DITracy/ContainerPanel.php',
    'Nette\\Bridges\\DatabaseDI\\DatabaseExtension' => $vendorDir . '/nette/database/src/Bridges/DatabaseDI/DatabaseExtension.php',
    'Nette\\Bridges\\DatabaseTracy\\ConnectionPanel' => $vendorDir . '/nette/database/src/Bridges/DatabaseTracy/ConnectionPanel.php',
    'Nette\\Bridges\\HttpDI\\HttpExtension' => $vendorDir . '/nette/http/src/Bridges/HttpDI/HttpExtension.php',
    'Nette\\Bridges\\HttpDI\\SessionExtension' => $vendorDir . '/nette/http/src/Bridges/HttpDI/SessionExtension.php',
    'Nette\\Bridges\\HttpTracy\\SessionPanel' => $vendorDir . '/nette/http/src/Bridges/HttpTracy/SessionPanel.php',
    'Nette\\Bridges\\MailDI\\MailExtension' => $vendorDir . '/nette/mail/src/Bridges/MailDI/MailExtension.php',
    'Nette\\Bridges\\ReflectionDI\\ReflectionExtension' => $vendorDir . '/nette/reflection/src/Bridges/ReflectionDI/ReflectionExtension.php',
    'Nette\\Bridges\\SecurityDI\\SecurityExtension' => $vendorDir . '/nette/security/src/Bridges/SecurityDI/SecurityExtension.php',
    'Nette\\Bridges\\SecurityTracy\\UserPanel' => $vendorDir . '/nette/security/src/Bridges/SecurityTracy/UserPanel.php',
    'Nette\\Caching\\Cache' => $vendorDir . '/nette/caching/src/Caching/Cache.php',
    'Nette\\Caching\\IBulkReader' => $vendorDir . '/nette/caching/src/Caching/IBulkReader.php',
    'Nette\\Caching\\IStorage' => $vendorDir . '/nette/caching/src/Caching/IStorage.php',
    'Nette\\Caching\\OutputHelper' => $vendorDir . '/nette/caching/src/Caching/OutputHelper.php',
    'Nette\\Caching\\Storages\\DevNullStorage' => $vendorDir . '/nette/caching/src/Caching/Storages/DevNullStorage.php',
    'Nette\\Caching\\Storages\\FileStorage' => $vendorDir . '/nette/caching/src/Caching/Storages/FileStorage.php',
    'Nette\\Caching\\Storages\\IJournal' => $vendorDir . '/nette/caching/src/Caching/Storages/IJournal.php',
    'Nette\\Caching\\Storages\\MemcachedStorage' => $vendorDir . '/nette/caching/src/Caching/Storages/MemcachedStorage.php',
    'Nette\\Caching\\Storages\\MemoryStorage' => $vendorDir . '/nette/caching/src/Caching/Storages/MemoryStorage.php',
    'Nette\\Caching\\Storages\\NewMemcachedStorage' => $vendorDir . '/nette/caching/src/Caching/Storages/NewMemcachedStorage.php',
    'Nette\\Caching\\Storages\\SQLiteJournal' => $vendorDir . '/nette/caching/src/Caching/Storages/SQLiteJournal.php',
    'Nette\\Caching\\Storages\\SQLiteStorage' => $vendorDir . '/nette/caching/src/Caching/Storages/SQLiteStorage.php',
    'Nette\\ComponentModel\\ArrayAccess' => $vendorDir . '/nette/component-model/src/ComponentModel/ArrayAccess.php',
    'Nette\\ComponentModel\\Component' => $vendorDir . '/nette/component-model/src/ComponentModel/Component.php',
    'Nette\\ComponentModel\\Container' => $vendorDir . '/nette/component-model/src/ComponentModel/Container.php',
    'Nette\\ComponentModel\\IComponent' => $vendorDir . '/nette/component-model/src/ComponentModel/IComponent.php',
    'Nette\\ComponentModel\\IContainer' => $vendorDir . '/nette/component-model/src/ComponentModel/IContainer.php',
    'Nette\\ComponentModel\\RecursiveComponentIterator' => $vendorDir . '/nette/component-model/src/ComponentModel/RecursiveComponentIterator.php',
    'Nette\\DI\\Compiler' => $vendorDir . '/nette/di/src/DI/Compiler.php',
    'Nette\\DI\\CompilerExtension' => $vendorDir . '/nette/di/src/DI/CompilerExtension.php',
    'Nette\\DI\\Config\\Adapters\\IniAdapter' => $vendorDir . '/nette/di/src/DI/Config/Adapters/IniAdapter.php',
    'Nette\\DI\\Config\\Adapters\\NeonAdapter' => $vendorDir . '/nette/di/src/DI/Config/Adapters/NeonAdapter.php',
    'Nette\\DI\\Config\\Adapters\\PhpAdapter' => $vendorDir . '/nette/di/src/DI/Config/Adapters/PhpAdapter.php',
    'Nette\\DI\\Config\\Helpers' => $vendorDir . '/nette/di/src/DI/Config/Helpers.php',
    'Nette\\DI\\Config\\IAdapter' => $vendorDir . '/nette/di/src/DI/Config/IAdapter.php',
    'Nette\\DI\\Config\\Loader' => $vendorDir . '/nette/di/src/DI/Config/Loader.php',
    'Nette\\DI\\Container' => $vendorDir . '/nette/di/src/DI/Container.php',
    'Nette\\DI\\ContainerBuilder' => $vendorDir . '/nette/di/src/DI/ContainerBuilder.php',
    'Nette\\DI\\ContainerLoader' => $vendorDir . '/nette/di/src/DI/ContainerLoader.php',
    'Nette\\DI\\DependencyChecker' => $vendorDir . '/nette/di/src/DI/DependencyChecker.php',
    'Nette\\DI\\Extensions\\ConstantsExtension' => $vendorDir . '/nette/di/src/DI/Extensions/ConstantsExtension.php',
    'Nette\\DI\\Extensions\\DIExtension' => $vendorDir . '/nette/di/src/DI/Extensions/DIExtension.php',
    'Nette\\DI\\Extensions\\DecoratorExtension' => $vendorDir . '/nette/di/src/DI/Extensions/DecoratorExtension.php',
    'Nette\\DI\\Extensions\\ExtensionsExtension' => $vendorDir . '/nette/di/src/DI/Extensions/ExtensionsExtension.php',
    'Nette\\DI\\Extensions\\InjectExtension' => $vendorDir . '/nette/di/src/DI/Extensions/InjectExtension.php',
    'Nette\\DI\\Extensions\\PhpExtension' => $vendorDir . '/nette/di/src/DI/Extensions/PhpExtension.php',
    'Nette\\DI\\Helpers' => $vendorDir . '/nette/di/src/DI/Helpers.php',
    'Nette\\DI\\MissingServiceException' => $vendorDir . '/nette/di/src/DI/exceptions.php',
    'Nette\\DI\\PhpGenerator' => $vendorDir . '/nette/di/src/DI/PhpGenerator.php',
    'Nette\\DI\\PhpReflection' => $vendorDir . '/nette/di/src/DI/PhpReflection.php',
    'Nette\\DI\\ServiceCreationException' => $vendorDir . '/nette/di/src/DI/exceptions.php',
    'Nette\\DI\\ServiceDefinition' => $vendorDir . '/nette/di/src/DI/ServiceDefinition.php',
    'Nette\\DI\\Statement' => $vendorDir . '/nette/di/src/DI/Statement.php',
    'Nette\\Database\\Connection' => $vendorDir . '/nette/database/src/Database/Connection.php',
    'Nette\\Database\\ConnectionException' => $vendorDir . '/nette/database/src/Database/exceptions.php',
    'Nette\\Database\\ConstraintViolationException' => $vendorDir . '/nette/database/src/Database/exceptions.php',
    'Nette\\Database\\Context' => $vendorDir . '/nette/database/src/Database/Context.php',
    'Nette\\Database\\Conventions\\AmbiguousReferenceKeyException' => $vendorDir . '/nette/database/src/Database/Conventions/AmbiguousReferenceKeyException.php',
    'Nette\\Database\\Conventions\\DiscoveredConventions' => $vendorDir . '/nette/database/src/Database/Conventions/DiscoveredConventions.php',
    'Nette\\Database\\Conventions\\StaticConventions' => $vendorDir . '/nette/database/src/Database/Conventions/StaticConventions.php',
    'Nette\\Database\\DriverException' => $vendorDir . '/nette/database/src/Database/DriverException.php',
    'Nette\\Database\\Drivers\\MsSqlDriver' => $vendorDir . '/nette/database/src/Database/Drivers/MsSqlDriver.php',
    'Nette\\Database\\Drivers\\MySqlDriver' => $vendorDir . '/nette/database/src/Database/Drivers/MySqlDriver.php',
    'Nette\\Database\\Drivers\\OciDriver' => $vendorDir . '/nette/database/src/Database/Drivers/OciDriver.php',
    'Nette\\Database\\Drivers\\OdbcDriver' => $vendorDir . '/nette/database/src/Database/Drivers/OdbcDriver.php',
    'Nette\\Database\\Drivers\\PgSqlDriver' => $vendorDir . '/nette/database/src/Database/Drivers/PgSqlDriver.php',
    'Nette\\Database\\Drivers\\Sqlite2Driver' => $vendorDir . '/nette/database/src/Database/Drivers/Sqlite2Driver.php',
    'Nette\\Database\\Drivers\\SqliteDriver' => $vendorDir . '/nette/database/src/Database/Drivers/SqliteDriver.php',
    'Nette\\Database\\Drivers\\SqlsrvDriver' => $vendorDir . '/nette/database/src/Database/Drivers/SqlsrvDriver.php',
    'Nette\\Database\\ForeignKeyConstraintViolationException' => $vendorDir . '/nette/database/src/Database/exceptions.php',
    'Nette\\Database\\Helpers' => $vendorDir . '/nette/database/src/Database/Helpers.php',
    'Nette\\Database\\IConventions' => $vendorDir . '/nette/database/src/Database/IConventions.php',
    'Nette\\Database\\IRow' => $vendorDir . '/nette/database/src/Database/IRow.php',
    'Nette\\Database\\IRowContainer' => $vendorDir . '/nette/database/src/Database/IRowContainer.php',
    'Nette\\Database\\IStructure' => $vendorDir . '/nette/database/src/Database/IStructure.php',
    'Nette\\Database\\ISupplementalDriver' => $vendorDir . '/nette/database/src/Database/ISupplementalDriver.php',
    'Nette\\Database\\NotNullConstraintViolationException' => $vendorDir . '/nette/database/src/Database/exceptions.php',
    'Nette\\Database\\ResultSet' => $vendorDir . '/nette/database/src/Database/ResultSet.php',
    'Nette\\Database\\Row' => $vendorDir . '/nette/database/src/Database/Row.php',
    'Nette\\Database\\SqlLiteral' => $vendorDir . '/nette/database/src/Database/SqlLiteral.php',
    'Nette\\Database\\SqlPreprocessor' => $vendorDir . '/nette/database/src/Database/SqlPreprocessor.php',
    'Nette\\Database\\Structure' => $vendorDir . '/nette/database/src/Database/Structure.php',
    'Nette\\Database\\Table\\ActiveRow' => $vendorDir . '/nette/database/src/Database/Table/ActiveRow.php',
    'Nette\\Database\\Table\\GroupedSelection' => $vendorDir . '/nette/database/src/Database/Table/GroupedSelection.php',
    'Nette\\Database\\Table\\IRow' => $vendorDir . '/nette/database/src/Database/Table/IRow.php',
    'Nette\\Database\\Table\\IRowContainer' => $vendorDir . '/nette/database/src/Database/Table/IRowContainer.php',
    'Nette\\Database\\Table\\Selection' => $vendorDir . '/nette/database/src/Database/Table/Selection.php',
    'Nette\\Database\\Table\\SqlBuilder' => $vendorDir . '/nette/database/src/Database/Table/SqlBuilder.php',
    'Nette\\Database\\UniqueConstraintViolationException' => $vendorDir . '/nette/database/src/Database/exceptions.php',
    'Nette\\DeprecatedException' => $vendorDir . '/nette/utils/src/Utils/exceptions.php',
    'Nette\\DirectoryNotFoundException' => $vendorDir . '/nette/utils/src/Utils/exceptions.php',
    'Nette\\FileNotFoundException' => $vendorDir . '/nette/utils/src/Utils/exceptions.php',
    'Nette\\Http\\Context' => $vendorDir . '/nette/http/src/Http/Context.php',
    'Nette\\Http\\FileUpload' => $vendorDir . '/nette/http/src/Http/FileUpload.php',
    'Nette\\Http\\Helpers' => $vendorDir . '/nette/http/src/Http/Helpers.php',
    'Nette\\Http\\IRequest' => $vendorDir . '/nette/http/src/Http/IRequest.php',
    'Nette\\Http\\IResponse' => $vendorDir . '/nette/http/src/Http/IResponse.php',
    'Nette\\Http\\ISessionStorage' => $vendorDir . '/nette/http/src/Http/ISessionStorage.php',
    'Nette\\Http\\Request' => $vendorDir . '/nette/http/src/Http/Request.php',
    'Nette\\Http\\RequestFactory' => $vendorDir . '/nette/http/src/Http/RequestFactory.php',
    'Nette\\Http\\Response' => $vendorDir . '/nette/http/src/Http/Response.php',
    'Nette\\Http\\Session' => $vendorDir . '/nette/http/src/Http/Session.php',
    'Nette\\Http\\SessionSection' => $vendorDir . '/nette/http/src/Http/SessionSection.php',
    'Nette\\Http\\Url' => $vendorDir . '/nette/http/src/Http/Url.php',
    'Nette\\Http\\UrlScript' => $vendorDir . '/nette/http/src/Http/UrlScript.php',
    'Nette\\Http\\UserStorage' => $vendorDir . '/nette/http/src/Http/UserStorage.php',
    'Nette\\IOException' => $vendorDir . '/nette/utils/src/Utils/exceptions.php',
    'Nette\\InvalidArgumentException' => $vendorDir . '/nette/utils/src/Utils/exceptions.php',
    'Nette\\InvalidStateException' => $vendorDir . '/nette/utils/src/Utils/exceptions.php',
    'Nette\\Iterators\\CachingIterator' => $vendorDir . '/nette/utils/src/Iterators/CachingIterator.php',
    'Nette\\Iterators\\Mapper' => $vendorDir . '/nette/utils/src/Iterators/Mapper.php',
    'Nette\\LegacyObject' => $vendorDir . '/nette/utils/src/Utils/LegacyObject.php',
    'Nette\\Loaders\\RobotLoader' => $vendorDir . '/nette/robot-loader/src/RobotLoader/RobotLoader.php',
    'Nette\\Localization\\ITranslator' => $vendorDir . '/nette/utils/src/Utils/ITranslator.php',
    'Nette\\Mail\\FallbackMailer' => $vendorDir . '/nette/mail/src/Mail/FallbackMailer.php',
    'Nette\\Mail\\FallbackMailerException' => $vendorDir . '/nette/mail/src/Mail/exceptions.php',
    'Nette\\Mail\\IMailer' => $vendorDir . '/nette/mail/src/Mail/IMailer.php',
    'Nette\\Mail\\Message' => $vendorDir . '/nette/mail/src/Mail/Message.php',
    'Nette\\Mail\\MimePart' => $vendorDir . '/nette/mail/src/Mail/MimePart.php',
    'Nette\\Mail\\SendException' => $vendorDir . '/nette/mail/src/Mail/exceptions.php',
    'Nette\\Mail\\SendmailMailer' => $vendorDir . '/nette/mail/src/Mail/SendmailMailer.php',
    'Nette\\Mail\\SmtpException' => $vendorDir . '/nette/mail/src/Mail/exceptions.php',
    'Nette\\Mail\\SmtpMailer' => $vendorDir . '/nette/mail/src/Mail/SmtpMailer.php',
    'Nette\\MemberAccessException' => $vendorDir . '/nette/utils/src/Utils/exceptions.php',
    'Nette\\Neon\\Decoder' => $vendorDir . '/nette/neon/src/Neon/Decoder.php',
    'Nette\\Neon\\Encoder' => $vendorDir . '/nette/neon/src/Neon/Encoder.php',
    'Nette\\Neon\\Entity' => $vendorDir . '/nette/neon/src/Neon/Entity.php',
    'Nette\\Neon\\Exception' => $vendorDir . '/nette/neon/src/Neon/Exception.php',
    'Nette\\Neon\\Neon' => $vendorDir . '/nette/neon/src/Neon/Neon.php',
    'Nette\\NotImplementedException' => $vendorDir . '/nette/utils/src/Utils/exceptions.php',
    'Nette\\NotSupportedException' => $vendorDir . '/nette/utils/src/Utils/exceptions.php',
    'Nette\\OutOfRangeException' => $vendorDir . '/nette/utils/src/Utils/exceptions.php',
    'Nette\\PhpGenerator\\ClassType' => $vendorDir . '/nette/php-generator/src/PhpGenerator/ClassType.php',
    'Nette\\PhpGenerator\\Closure' => $vendorDir . '/nette/php-generator/src/PhpGenerator/Closure.php',
    'Nette\\PhpGenerator\\Constant' => $vendorDir . '/nette/php-generator/src/PhpGenerator/Constant.php',
    'Nette\\PhpGenerator\\Factory' => $vendorDir . '/nette/php-generator/src/PhpGenerator/Factory.php',
    'Nette\\PhpGenerator\\GlobalFunction' => $vendorDir . '/nette/php-generator/src/PhpGenerator/GlobalFunction.php',
    'Nette\\PhpGenerator\\Helpers' => $vendorDir . '/nette/php-generator/src/PhpGenerator/Helpers.php',
    'Nette\\PhpGenerator\\Method' => $vendorDir . '/nette/php-generator/src/PhpGenerator/Method.php',
    'Nette\\PhpGenerator\\Parameter' => $vendorDir . '/nette/php-generator/src/PhpGenerator/Parameter.php',
    'Nette\\PhpGenerator\\PhpFile' => $vendorDir . '/nette/php-generator/src/PhpGenerator/PhpFile.php',
    'Nette\\PhpGenerator\\PhpLiteral' => $vendorDir . '/nette/php-generator/src/PhpGenerator/PhpLiteral.php',
    'Nette\\PhpGenerator\\PhpNamespace' => $vendorDir . '/nette/php-generator/src/PhpGenerator/PhpNamespace.php',
    'Nette\\PhpGenerator\\Property' => $vendorDir . '/nette/php-generator/src/PhpGenerator/Property.php',
    'Nette\\PhpGenerator\\Traits\\CommentAware' => $vendorDir . '/nette/php-generator/src/PhpGenerator/Traits/CommentAware.php',
    'Nette\\PhpGenerator\\Traits\\FunctionLike' => $vendorDir . '/nette/php-generator/src/PhpGenerator/Traits/FunctionLike.php',
    'Nette\\PhpGenerator\\Traits\\NameAware' => $vendorDir . '/nette/php-generator/src/PhpGenerator/Traits/NameAware.php',
    'Nette\\PhpGenerator\\Traits\\VisibilityAware' => $vendorDir . '/nette/php-generator/src/PhpGenerator/Traits/VisibilityAware.php',
    'Nette\\Reflection\\Annotation' => $vendorDir . '/nette/reflection/src/Reflection/Annotation.php',
    'Nette\\Reflection\\AnnotationsParser' => $vendorDir . '/nette/reflection/src/Reflection/AnnotationsParser.php',
    'Nette\\Reflection\\ClassType' => $vendorDir . '/nette/reflection/src/Reflection/ClassType.php',
    'Nette\\Reflection\\Extension' => $vendorDir . '/nette/reflection/src/Reflection/Extension.php',
    'Nette\\Reflection\\GlobalFunction' => $vendorDir . '/nette/reflection/src/Reflection/GlobalFunction.php',
    'Nette\\Reflection\\Helpers' => $vendorDir . '/nette/reflection/src/Reflection/Helpers.php',
    'Nette\\Reflection\\IAnnotation' => $vendorDir . '/nette/reflection/src/Reflection/IAnnotation.php',
    'Nette\\Reflection\\Method' => $vendorDir . '/nette/reflection/src/Reflection/Method.php',
    'Nette\\Reflection\\Parameter' => $vendorDir . '/nette/reflection/src/Reflection/Parameter.php',
    'Nette\\Reflection\\Property' => $vendorDir . '/nette/reflection/src/Reflection/Property.php',
    'Nette\\Security\\AuthenticationException' => $vendorDir . '/nette/security/src/Security/AuthenticationException.php',
    'Nette\\Security\\IAuthenticator' => $vendorDir . '/nette/security/src/Security/IAuthenticator.php',
    'Nette\\Security\\IAuthorizator' => $vendorDir . '/nette/security/src/Security/IAuthorizator.php',
    'Nette\\Security\\IIdentity' => $vendorDir . '/nette/security/src/Security/IIdentity.php',
    'Nette\\Security\\IResource' => $vendorDir . '/nette/security/src/Security/IResource.php',
    'Nette\\Security\\IRole' => $vendorDir . '/nette/security/src/Security/IRole.php',
    'Nette\\Security\\IUserStorage' => $vendorDir . '/nette/security/src/Security/IUserStorage.php',
    'Nette\\Security\\Identity' => $vendorDir . '/nette/security/src/Security/Identity.php',
    'Nette\\Security\\Passwords' => $vendorDir . '/nette/security/src/Security/Passwords.php',
    'Nette\\Security\\Permission' => $vendorDir . '/nette/security/src/Security/Permission.php',
    'Nette\\Security\\SimpleAuthenticator' => $vendorDir . '/nette/security/src/Security/SimpleAuthenticator.php',
    'Nette\\Security\\User' => $vendorDir . '/nette/security/src/Security/User.php',
    'Nette\\SmartObject' => $vendorDir . '/nette/utils/src/Utils/SmartObject.php',
    'Nette\\StaticClass' => $vendorDir . '/nette/utils/src/Utils/StaticClass.php',
    'Nette\\StaticClassException' => $vendorDir . '/nette/utils/src/Utils/exceptions.php',
    'Nette\\UnexpectedValueException' => $vendorDir . '/nette/utils/src/Utils/exceptions.php',
    'Nette\\Utils\\ArrayHash' => $vendorDir . '/nette/utils/src/Utils/ArrayHash.php',
    'Nette\\Utils\\ArrayList' => $vendorDir . '/nette/utils/src/Utils/ArrayList.php',
    'Nette\\Utils\\Arrays' => $vendorDir . '/nette/utils/src/Utils/Arrays.php',
    'Nette\\Utils\\AssertionException' => $vendorDir . '/nette/utils/src/Utils/exceptions.php',
    'Nette\\Utils\\Callback' => $vendorDir . '/nette/utils/src/Utils/Callback.php',
    'Nette\\Utils\\DateTime' => $vendorDir . '/nette/utils/src/Utils/DateTime.php',
    'Nette\\Utils\\FileSystem' => $vendorDir . '/nette/utils/src/Utils/FileSystem.php',
    'Nette\\Utils\\Finder' => $vendorDir . '/nette/finder/src/Utils/Finder.php',
    'Nette\\Utils\\Html' => $vendorDir . '/nette/utils/src/Utils/Html.php',
    'Nette\\Utils\\IHtmlString' => $vendorDir . '/nette/utils/src/Utils/IHtmlString.php',
    'Nette\\Utils\\Image' => $vendorDir . '/nette/utils/src/Utils/Image.php',
    'Nette\\Utils\\ImageException' => $vendorDir . '/nette/utils/src/Utils/exceptions.php',
    'Nette\\Utils\\Json' => $vendorDir . '/nette/utils/src/Utils/Json.php',
    'Nette\\Utils\\JsonException' => $vendorDir . '/nette/utils/src/Utils/exceptions.php',
    'Nette\\Utils\\ObjectHelpers' => $vendorDir . '/nette/utils/src/Utils/ObjectHelpers.php',
    'Nette\\Utils\\ObjectMixin' => $vendorDir . '/nette/utils/src/Utils/ObjectMixin.php',
    'Nette\\Utils\\Paginator' => $vendorDir . '/nette/utils/src/Utils/Paginator.php',
    'Nette\\Utils\\Random' => $vendorDir . '/nette/utils/src/Utils/Random.php',
    'Nette\\Utils\\Reflection' => $vendorDir . '/nette/utils/src/Utils/Reflection.php',
    'Nette\\Utils\\RegexpException' => $vendorDir . '/nette/utils/src/Utils/exceptions.php',
    'Nette\\Utils\\Strings' => $vendorDir . '/nette/utils/src/Utils/Strings.php',
    'Nette\\Utils\\UnknownImageFileException' => $vendorDir . '/nette/utils/src/Utils/exceptions.php',
    'Nette\\Utils\\Validators' => $vendorDir . '/nette/utils/src/Utils/Validators.php',
    'Tester\\Assert' => $vendorDir . '/nette/tester/src/Framework/Assert.php',
    'Tester\\AssertException' => $vendorDir . '/nette/tester/src/Framework/AssertException.php',
    'Tester\\CodeCoverage\\Collector' => $vendorDir . '/nette/tester/src/CodeCoverage/Collector.php',
    'Tester\\CodeCoverage\\Generators\\AbstractGenerator' => $vendorDir . '/nette/tester/src/CodeCoverage/Generators/AbstractGenerator.php',
    'Tester\\CodeCoverage\\Generators\\CloverXMLGenerator' => $vendorDir . '/nette/tester/src/CodeCoverage/Generators/CloverXMLGenerator.php',
    'Tester\\CodeCoverage\\Generators\\HtmlGenerator' => $vendorDir . '/nette/tester/src/CodeCoverage/Generators/HtmlGenerator.php',
    'Tester\\CodeCoverage\\PhpParser' => $vendorDir . '/nette/tester/src/CodeCoverage/PhpParser.php',
    'Tester\\DataProvider' => $vendorDir . '/nette/tester/src/Framework/DataProvider.php',
    'Tester\\DomQuery' => $vendorDir . '/nette/tester/src/Framework/DomQuery.php',
    'Tester\\Dumper' => $vendorDir . '/nette/tester/src/Framework/Dumper.php',
    'Tester\\Environment' => $vendorDir . '/nette/tester/src/Framework/Environment.php',
    'Tester\\FileMock' => $vendorDir . '/nette/tester/src/Framework/FileMock.php',
    'Tester\\FileMutator' => $vendorDir . '/nette/tester/src/Framework/FileMutator.php',
    'Tester\\Helpers' => $vendorDir . '/nette/tester/src/Framework/Helpers.php',
    'Tester\\Runner\\CliTester' => $vendorDir . '/nette/tester/src/Runner/CliTester.php',
    'Tester\\Runner\\CommandLine' => $vendorDir . '/nette/tester/src/Runner/CommandLine.php',
    'Tester\\Runner\\Job' => $vendorDir . '/nette/tester/src/Runner/Job.php',
    'Tester\\Runner\\OutputHandler' => $vendorDir . '/nette/tester/src/Runner/OutputHandler.php',
    'Tester\\Runner\\Output\\ConsolePrinter' => $vendorDir . '/nette/tester/src/Runner/Output/ConsolePrinter.php',
    'Tester\\Runner\\Output\\JUnitPrinter' => $vendorDir . '/nette/tester/src/Runner/Output/JUnitPrinter.php',
    'Tester\\Runner\\Output\\Logger' => $vendorDir . '/nette/tester/src/Runner/Output/Logger.php',
    'Tester\\Runner\\Output\\TapPrinter' => $vendorDir . '/nette/tester/src/Runner/Output/TapPrinter.php',
    'Tester\\Runner\\PhpInterpreter' => $vendorDir . '/nette/tester/src/Runner/PhpInterpreter.php',
    'Tester\\Runner\\Runner' => $vendorDir . '/nette/tester/src/Runner/Runner.php',
    'Tester\\Runner\\Test' => $vendorDir . '/nette/tester/src/Runner/Test.php',
    'Tester\\Runner\\TestHandler' => $vendorDir . '/nette/tester/src/Runner/TestHandler.php',
    'Tester\\TestCase' => $vendorDir . '/nette/tester/src/Framework/TestCase.php',
    'Tester\\TestCaseException' => $vendorDir . '/nette/tester/src/Framework/TestCase.php',
    'Tracy\\Bar' => $vendorDir . '/tracy/tracy/src/Tracy/Bar.php',
    'Tracy\\BlueScreen' => $vendorDir . '/tracy/tracy/src/Tracy/BlueScreen.php',
    'Tracy\\Bridges\\Nette\\Bridge' => $vendorDir . '/tracy/tracy/src/Bridges/Nette/Bridge.php',
    'Tracy\\Bridges\\Nette\\MailSender' => $vendorDir . '/tracy/tracy/src/Bridges/Nette/MailSender.php',
    'Tracy\\Bridges\\Nette\\TracyExtension' => $vendorDir . '/tracy/tracy/src/Bridges/Nette/TracyExtension.php',
    'Tracy\\Debugger' => $vendorDir . '/tracy/tracy/src/Tracy/Debugger.php',
    'Tracy\\DefaultBarPanel' => $vendorDir . '/tracy/tracy/src/Tracy/DefaultBarPanel.php',
    'Tracy\\Dumper' => $vendorDir . '/tracy/tracy/src/Tracy/Dumper.php',
    'Tracy\\FireLogger' => $vendorDir . '/tracy/tracy/src/Tracy/FireLogger.php',
    'Tracy\\Helpers' => $vendorDir . '/tracy/tracy/src/Tracy/Helpers.php',
    'Tracy\\IBarPanel' => $vendorDir . '/tracy/tracy/src/Tracy/IBarPanel.php',
    'Tracy\\ILogger' => $vendorDir . '/tracy/tracy/src/Tracy/ILogger.php',
    'Tracy\\Logger' => $vendorDir . '/tracy/tracy/src/Tracy/Logger.php',
    'Tracy\\OutputDebugger' => $vendorDir . '/tracy/tracy/src/Tracy/OutputDebugger.php',
);
