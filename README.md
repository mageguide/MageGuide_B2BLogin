# MageGuide B2BLogin
Tested on: ```2.3.x```

## Description
Allows B2B admin control over who can register in the website adding an approval customer attribute

## Functionalities 
- Adds Approve/Disapprove buttons in the admin area of the Customer view
- Informs Customer by email about the admin's decision

## Installation
- Upload module files in ``app/code/MageGuide/B2BLogin``
- Install module
```sh
        $ php bin/magento module:enable MageGuide_B2BLogin
        $ php bin/magento setup:upgrade
        $ php bin/magento setup:di:compile
```

## Dependencies
Uses ```Amasty_CustomerAttributes``` to create and handle the default value of the approval customer attribute

## Screenshots
Desktop

![Alt text](/Screenshots/MageGuide_B2BLogin_desktop.png?raw=true)


