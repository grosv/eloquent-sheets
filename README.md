# Laravel Package Repository Template
A bare bones respository template for developing Laravel packages.
### How To Use
Use the green "Use this template" button above to create a new repository based on this repo. You'll get a fresh repository reflecting the code as it is right now in this repo, but with a fresh commit history.

After you've done that, you have some editing / refactoring to do. Depending on your IDE the instructions and difficulty of this will vary a bit. But however you accomplish them, here are your steps:

1. Edit composer.json to change the vendor/packagename line, the description, the author name, and the author email. Also update the autoload blocks to reflect your vendor namespace and package name.

2. Rename LaravelPackageTemplateProvider.php to be {YourPackageName}Provider.php and change the class name inside to match. Change the namespace to match your vendor namespace.

3. Go to tests/TestCase and change the name of the loaded service provider to match your package name.

4. Run the tests. There should be one test and it should pass. If that happens, you know you at least haven't broken the test setup and should be ready to start building something great.

### Acknowledgements

I started off writing packages with the help of Marcel Pociot's excellent [Laravel Package Boilerplate](https://laravelpackageboilerplate.com/#/) and I purchased and used his course on package development. Without those two things I'd never have gotten to be nearly as proficient in package develpment as I am. If you are just starting out, I strongly recommend using those vs using this template or starting from scratch.
