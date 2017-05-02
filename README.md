## Exercice for Benito

# 0 - Build the full FrameWork from OCRoom : Done !

# 1 - Add Feature : Allow logged user to logout :
* Add a link in menu to logout the connected user. 
* Make a new action in ConnexionController.
* After logout, redirect to homepage. 

# 2 - Refactoring user managment :
* Store user in Database : Create table(s) according to DreamCentury database nomenclature.
* Add a new Manager to work the new table(s) (according to the Framework manager name nomenclature).
* Update your ConnexionController code.
* Adding a subscribtion form : Input list [ login, passwod, password confirmation, email, email confirmation ].

# 2.1 - Two types of user. 
* The users who subrscribe with subscription form are now simple writers. They are not a full administrator and they can only edit their news.

# 3 - CSS : Fix the CSS error when text is too long.
* Try to enter a very long text in title and text of a News and observe the result. (Long text without space)
* Fix visual problem by adding somes CSS rules.

# 4 - Fix SQL Injection : 
* If you have good memory, you know that SQL Injection mean ! 
* Remove all SQL Injection vulnerabilities. 

# 5 - Fix JavaScript Injection :
* Google is your best friend.
* Remove all JavaScript Injection vulnerabilities. 

# 6 - Improve your code : Url and Link
Actually, you need to enter manually the value of a href attribute according to the route.xml file. 
What happens if tomorrow i decided to edit a root ?
All your code is break down.
This part consist to add a functionnality for ask a route for a Controller and an Action using a function. Replace the manually entered href by a call of this function. 

# 7 - Add Feature : Ajax ! Flower Party :)
* Change the comportment of the form to add a comment in a news page.
* The form are include in the same page of the news. On top and bottom of comment list.
* Form to add new comment must work now with ajax. So when user submit his form, don't reload the page but post an ajax query to valid the form.
* Show errors or add new comment directly if there are no error.
* The HTTP Response of any ajax call must be a JSON Object.
* Use jQuery to make your ajax query. 
