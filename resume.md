2.1 Command pattern
3.1 Adding actions to the game
3.2 Implement execute method
4.1 We're ready to add more actions to our game and allow players to choose their actions. First, we need to create an interface for our commands
4.2 Asking the player to choose an action
5.1 Undoing our actions
 There we were... in the middle of a fierce battle and... uh, what's that? We lost? No way! Our opponent got super lucky. Surely there's a way to undo that operation and try again, right? There is - with the command pattern.
5.3 All right! It's time to ask the player if they want to revert the last action in case of defeat. I'll close a few files and go back to GameApplication. Find the AI's turn, and inside this if() where we check if the player died, write $undoChoice = GameApplication::$printer->confirm(). The question will read:

  You've lost! Do you want to undo your last turn?.

 If the answer is "no", we need to end the battle and exit, so I'll move these two lines inside the if.

 If the answer is "yes", we undo actions from the last turn, which means that we need to call undo() on the command objects. But we can't just undo these commands in any order. We need to undo them in the reverse order that they were executed... or weird things could happen. This is basically a "FILO" stack - "First In, Last Out".
7.1 Chain of responsability
 Time for design pattern number two - the Chain of Responsibility pattern. Sometimes, an official definition for a pattern doesn't exist. This is no exception, so here's my definition. Put simply, Chain of Responsibility is a way to set up a sequence of methods to be executed, where each method can decide to execute the next one in the chain or stop the sequence entirely.

 When we have to run a sequence of checks to determine what to do next, this pattern can help us do that. Suppose we want to check to see if a comment is spam or not, and we have five different algorithms to help us make that determination. If any of them return true, it means the comment is spam and we should stop the process because running algorithms is expensive. In a situation like this, we need to encapsulate each algorithm into a handler class, set up the chain, and run it.
 
 For our next challenge, we're going to boost the player's level. To do that, we'll reward players with extra XP after a battle. We can reward them in a few different ways, but only one should apply at a time. The conditions for XP rewards are as follows:

 One: If the player is level 1. Two: If the player has won 3 times or more in a row. And three, to add some randomness, the player will throw two six-sided dice. They win if a pair is rolled, but if the result is 7, they do not.

 Each condition will reward the player with 25 XP.
8.1 Triggering chain of responsability manually
9.1 This is a Symfony app, so let's take advantage of that and use the autoconfigure feature to set up our chain. There's even a super useful Autoconfigure attribute we can use in our handler classes.
9.3 Bonus: Null Object Pattern
 - Ready for a bonus topic? It's time to talk about the Null Object pattern. What is the Null Object pattern? In a nutshell, it's a smart way to avoid null checks. Instead of checking to see if a property is null, as we've done in the past, we'll create a "null object" that implements the same interface and does nothing in their methods. What does this mean? Put simply, if a method returns a value, it will return as close to null as possible. For example, if it returns an array, you'd return an empty array. A string? Return an empty string. An int? Return 0. It can get even more complicated that this, but you get the idea.
9.4 Okay, let's keep going! Open up CasinoHandler and add a constructor where we'll initialize $this->next to a new NullHandler() object. Copy this constructor because we'll need it for the other handlers.
9.5 Everything's working and the code looks great, but I know a little trick that could make this even better. We could make the  handler property a constructor argument and inject NullHandler into the last one in the chain using the Autowire attribute we've seen before, like this
 This would even allow us to remove the setNext() method from the interface, which is pretty handy.
10 middleware pattern
 chain of responsability used in symfony security-core
11 State pattern
 State is a way to organize your code so that an object can change its behavior when its internal state changes. It helps you represent different states as separate classes and allows the object to switch between these states seamlessly.

 How does it do that? Let's check it out!

 Pattern Anatomy
 - The State pattern consists of three elements:

  -A Context class that represents the object whose behavior changes based on its internal state, and has a reference to the current state object.

  -A common interface for all concrete state classes. This declares methods that represent actions that can be taken in each state.
 Imaginary Example
 - Suppose we have a publishPost() function that will do different things based on the status of an article. If the article is a draft, it will change the status to "moderation" and notify the moderator. If it's already in moderation and the user is an admin, it will change the status to "published" and send a tweet.

 public function publishPost(Article $article) {
    if ($article->getStatus() === 'draft') {
        $article->setStatus('moderation');
        $this->notifyModerator();
    } elseif ($article->getStatus() === 'moderation') {
        if ($this->getCurrentUser()->isAdmin()) {
            $article->setStatus('published');
        }
        $this->sendTweet($article);
    } elseif ($article->getStatus() === 'published') {
        throw new AlreadyPublishedException();
    }
}
 If we apply the State pattern to this function, we need to create a class for each state: DraftState, ModerationState, and PublishedState. Each class will have a publish() method that encapsulates the specific logic for that state, so the DraftState class would look something like this:

 class DraftState implements StateInterface {
    public function publish(Article $article) {
        $article->setStatus('moderation');
        $this->notifyModerator();
    }
 }
12.1 Handle didiculty with state pattern
 - Alright, we're ready to add some states. Create a new PHP class inside the same folder, and instead of using numbers to represent difficulty levels, we're going to name them - "Easy", "Medium", etc. So let's name this EasyState, make it implement the interface, and hold "option" + "enter" to add the methods.
13 State vs. Strategy
 - Another place we can see the State pattern at work is in the Symfony Workflow component
 Did you know that the State and Strategy patterns share the same design? The only structural difference is that state objects may have a reference to other states. So... if both patterns have the same design, why does it matter which one we use? Won't we get the same result either way? Not exactly. The most important difference between each pattern is the purpose behind them. The State pattern allows us to change behavior based on the internal state of an object. The Strategy pattern allows us to choose from a family of algorithms, regardless of the state of the system.

 Here's an excellent analogy from Eugene Kovko and Michal Aibin:

 A car can be in different states. The engine can be on, and the engine can be off. The battery can be dead. The tank can be empty, and so on. In all of these states, the car will behave differently. However, a driver can access the car’s interface: steering wheel, pedals, gears, etc. These are the states, and the entire behavior can be considered a combination of the conditions. All the states would provide a distinct behavior [...]

This is the State pattern in action. But, if we change the engine to use gas or diesel, it does not change the state of the car it only changes how an internal element of the system works. That's the Strategy pattern.
14.1 Factory Pattern
 Pattern Anatomy
 - The Factory pattern is composed of five parts:

  The first part is an interface of the products we want to create. If we wanted to create weapons for our characters, for example, we'd have a WeaponInterface, and the products would be weapons.

  Second are the concrete products that implement the interface. In this example, we would have classes like Sword, Axe, Bow, and so on.

  Third is the factory interface. This is optional, but it's super useful when you need to create families of products.

  Fourth is the concrete factory that implements the factory interface if we have one. This class knows everything about creating products.

  And lastly, we have the client, which uses a factory to create product objects. This class only knows how to use products, but not how they're created, or what specific product it happens to be using.
  Factory with Multiple Make Methods
  Factory with a Single Make Method
  Abstract Factory
15.1 Abstract Factory Pattern
 - As we saw in the previous chapter, an abstract factory allows us to handle families of objects. To illustrate this, we're introducing cheat codes to the game that, if activated, will give us some super powerful weapons. Oh yeah, now we can really rule the game! To add cheat codes, we'll need to create another factory and a way to swap it at runtime.
15.4 we need to add a way to swap our factories at runtime
16 
 Using Factories with Symfony
 - Check it out! Go to the Symfony docs site, search for "Using Factory", and click on the first link here.
 
