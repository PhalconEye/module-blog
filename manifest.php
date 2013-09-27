<?php 

return new \Phalcon\Config(array (
  'type' => 'module',
  'name' => 'blog',
  'title' => 'Blog',
  'description' => 'PhalconEye Blog module.',
  'version' => '0.1.0',
  'author' => 'PhalconEye Team',
  'website' => 'http://phalconeye.com/',
  'dependencies' => 
  array (
    0 => 
    array (
      'name' => 'core',
      'type' => 'module',
      'version' => '0.4.0',
    ),
    1 => 
    array (
      'name' => 'user',
      'type' => 'module',
      'version' => '0.4.0',
    ),
  ),
));