EVENTFUL // CAKEPHP
======================
Event-driven programming for CakePHP

----

/!\ Repo Layout Updated /!\ 
----------------------
For the old folder layout check the `legacy` branch.
The new master branch featuring this README will be written on 1.3 and undergo some 
major changes in the near future.

----


Installation
----------------------
Just clone/unzip this repo into your `plugins` directory.

```shell
cd app/plugins
git clone git://github.com/m3nt0r/eventful-cakephp.git eventful
```


Conventions
----------------------
Inside your `app/controllers` and `app/models` directories simply create 
new php files with the "_events.php" suffix. Here is a working example listing.

```
    -rw-r--r--@  users_controller.php
    -rw-r--r--@  users_events.php
    -rw-r--r--@  pages_controller.php
    -rw-r--r--@  pages_events.php
    ...
```

Inside the event handler classes you must prefix all methods with the `on` keyword.

```
    // example dispatch
    $this->Event->dispatch('paypalComplete');
    $this->Event->dispatch('paypalCancel');
    
    // good
    function onPaypalComplete($event);
    function onPaypalCancel($event);
    
    // bad
    function paypalComplete($event);    
    function paypal_cancel($event);
```

Code Example
----------------------
I assume you already know about CRUD and other CakePHP terminology. The following is 
not a working program and should only give you an idea of how the system was designed.

### Trigger from a controller
In a controller, include the EventComponent and use its 'dispatch' method to 
trigger an event. The first parameter sets which name the responding event handler
should have. In this case we allow "onSignup"-handlers to respond.

```php
class UsersController extends AppController {
  public $components = array('Eventful.Event');
  public function add() { 
    // save() etc... 
    // now tell other parts of the application what happened
    $response = $this->Event->dispatch('signup', array(
      'user' => $this->User->data
    ));
  }
}
```

### Example Event Classes
You may have any number of event classes and they may contain any number
of methods (handlers). Every class is examined once an event is dispatched and
check if the class is able to handle the request. The example above will run 
the code within `onSignup` in both classes.
```php
// statistics_events.php
class StatisticEvents extends AppControllerEvents {
  public function onSignup($event) { 
    ClassRegistry::init('Statistic')->incrUsers();
  }	
  public function onVisit($event) { 
    ClassRegistry::init('Statistic')->incrVisits();
  }
  // and others...
}

// mailing_events.php
class MailingEvents extends AppControllerEvents {
  public function onSignup($event) { 
    $userEmail = $event->user['email'];
    // ... send mail or smth
    return array('sentVerify' => $result);
  }	
}
```

### The Result
Back in our controller we could now examine the response of each handler and
maybe change the outcome of the action. 

```php
class UsersController extends AppController {
  public $components = array('Eventful.Event');
  public function add() { 
    // post-save()
    $response = $this->Event->dispatch('signup', array(
      'user' => $this->User->data
    ));
    
    // lets evaluate the response
    if (!empty($response['MailingEvents']) 
    and $response['MailingEvents']['sentVerify']) {
      $this->Session->setFlash('Check your inbox for verification.');
    }
  }
}
```

### The Alternative
However, you can also move this logic inside the event handler. I recommend doing so
for portability reasons. Inside the action would require managing lots of conditions
and constraints, which is not very flexible. You may access the object from which the
event originates via the `Controller` (or `Model`) property in the `$event`-param.
```php
class MailingEvents extends AppControllerEvents {
  public function onSignup($event) { 
    $userEmail = $event->user['email'];
    
    $result = $this->Email->send(); // or something..
    if ($result == true) {
      $session = event->Controller->Session;
      $session->setFlash('Check your inbox for verification.');
    }
    return array('sentVerify' => $result);
  }	
}
```

License
----------------------
MIT License  
Copyright (c) 2008-2013, Kjell Bublitz.  
See LICENSE for more info.