# Delete Hidden Posts

This is a plugin for [Question2Answer](https://github.com/q2a/question2answer) websites that deletes all hidden posts
with/with-out having children posts

The latest version of Question2Answer does not allow to delete the posts directly which have some children posts (the
comments and answers for a question are said to be children posts for that question and the comments to a answer are
said to be children posts to a answer ). You must have to hide and then delete all its child posts one by one before you
delete the parent one. This applies to both the Quesions and answers as well.

This plugin adds a Delete button right after the question / answer / comment (can be controlled via admin panel options)
which can delete the posts (question / answer / comment) with a single click.

Also if your website has many hidden posts which have many child posts this plugin can delete them with one click
effort.

## Features

- Ability to delete all the hidden posts from Question2Answer database having dependencies
- When a question is deleted all its Answers, Comments are deleted
- When an answer is deleted all its Comments and Related Questions are deleted
- Buttons are displayed *after* a Question's or Answer's or Comment's response buttons - which helps to delete the post
  with one click
  
## Author


The original author is [Amiya Sahu](https://www.question2answer.org/qa/user/Ami). 
I ([pupi1985](https://www.question2answer.org/qa/user/pupi1985)) am just extending the original plugin.

## Disclaimer

This is **beta** code. It is probably okay for production environments, but may not work exactly as expected. Refunds
will not be given. If it breaks, you get to keep both parts.

## About Question2Answer

Question2Answer is a free and open source platform for Q&A sites. For more information, visit:

https://www.question2answer.org/
