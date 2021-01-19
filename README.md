Mail Bundle
===

This is a heavily forked project from https://github.com/extellient/MailBundle which now only bears similarity in some of the Interfaces. 
The main changes were to upgrade to use symfony/mail, to remove Doctrine as a Sending Strategy, to switch to using MailTemplate and to allow building of the mail template ahead of time. 

This Bundle has been uploaded because someone asked to see how I modified the underlying fork. Use this at your own discretion. It is not a plug-and-play bundle and would require manual drop-in.

What are Changes Made in This Bundle compared to the MailBundle
--

1. Because I didn't intend to use this as a Bundle, (I knew I'd need more modifications) I updated the Entity namespaces and moved some of the interfaces around to suit my project
https://github.com/Kalyse/ConceptMailBundle/tree/master/Entity
   
2. Internally, my application sends Mails based on Events which are sent. ie. If a Guest makes a reservation for a booking/property, there is a "ReservationCreatedEvent" which is dispatched. 
I decided to create subs for all mail that are sent. 
   https://github.com/Kalyse/ConceptMailBundle/blob/master/Mail/EventSubscriber/ReservationGuestConfirmationSubscriber.php
   
3. I changed the method signature responsible for sending mail. I prefer 

```
$message = $this->mailer->createTemplatedEmail(
    $template->getCode(),
    $mail,
    $variables
);

$this->mailer->send($message);
```

4. The idea here is that we pass a template code, something like `foo-bar-template`, and the Template Code is fetched from the database. 

For example, a `mail_template` might look like:

```sql
insert into symfony.mail_template (id, owner_id, channel_id, created_at, updated_at, mail_subject, mail_body, code)
values (null, null, null, '2021-01-16 14:41:27', null, 'You have received a new booking.',
        '@Mails/reservationStakeholderCreated.html.twig', 'reservation-stakeholder');
```

The changes I made were to associate a template with a "User" and some domain specific relationship called a "Channel" (which you don't need to know about).

Because I didn't want raw twig templates storing in database, outside of version control, (unless they are user generator), I decided to allow paths to templates to be created.

5. @Mails/reservationStakeholderCreated.html.twig is just a Twig namespace path to https://github.com/Kalyse/ConceptMailBundle/tree/master/Mail/templates
I created a 

```
twig:
    paths:
        # used in mail tempaltes paths like @Mail/foo.html.twig
        '%kernel.project_dir%/src/Mail/templates': Mails
```

6. Added inky bundle for easier mail templating.  https://github.com/foundation/inky

7. In previous iterations of mail bundles I've created, I would store the serialized Entities used to send mail. This ended up meaning each mail could be 200kb+ in size just in storage for unused data which really didn't need to be stored. 
In this approach, I decided that I wanted to pass entities to a Mail Template, but then use my existing JMS serialize configuration to serialize and then flatted the structure into dot.notation.array.structure. 
You can see how https://github.com/Kalyse/ConceptMailBundle/blob/master/Mail/Template/VariableGenerator.php takes the variables and just returns a variable context which is then eventually passed to the Mail. We check to see if the object passed is capable of being managed through Doctrine (using isTransient) and then additionally, if an email is already passed, we can just wrap the email here.
The reason why we do this: https://github.com/Kalyse/ConceptMailBundle/blob/master/Mail/Template/VariableGenerator.php#L62 is because I wanted to take advantage of this method https://github.com/symfony/symfony/blob/5.x/src/Symfony/Bridge/Twig/Mime/WrappedTemplatedEmail.php#L38 so that I can embed images in my emails... such as
   https://github.com/Kalyse/ConceptMailBundle/blob/master/Mail/templates/snippets/reservationPayments.html.twig#L13
   
8. Final change, eventually I need to be able to attach Mails which are sent to specific users, so we have a good log. Once a mail is sent it's stored in database, however, the previous bundle worked by:
    
    1. Configure Email Entity. 
   
    2. Queue Email Entity ready for sending
    
    3. Run command which grabs all the mail from the Doctrine which haven't been sent. 
    

I've been there before with other bundles which do this and the flexibility just lacks. I much prefer the following approach:

    1. Create Mail Message 

    2. Send to Symfony/Messenger

    3. Save in Database as Entity


Doing things this way, means that you immediately have access to the Message rather than the other way around. Utilising symfony/messenger means you can still delegate sending to an async process as well, so there's just no reason to bundle up messages in database as a strategy. 



    
    

  
   
