#!/usr/bin/python3
# -*- coding: utf-8 -*-

# Polymath Ventures Engineering Challenge
# done by Michael Aitbayev (ca74224497@gmail.com).

# First of all I want to make one essential remark that I have never dealt with a python before.
# So excuse me if something goes wrong xD.

# Part I.

# Perform request to Ebay API.
# Probably, better way to use an official Paython SDK
# (https://developer.ebay.com/tools/sdks) for this purpose,
# but according to chellenge conditions i won't.

# Core modules are required for work.
# To improve performance you can put inside the functions that use them.
#===============================================================================
import os
import sys
import json
import logging
import requests
import sqlite3
from sqlite3 import Error
from xml.etree import ElementTree
from datetime import datetime
#===============================================================================


# Class for working with ebay requests
# if we had many types of request we could use 'template method' pattern,
# but we have only one
#===============================================================================
class EBayRequest:
    # Token may be stored other way (.ini file, database, config-class, etc.)
    # hard-coded only for simplification
    EBAY_API_TOKEN = 'AgAAAA**AQAAAA**aAAAAA**deDIWw**nY+sHZ2PrBmdj6wVnY+sEZ2PrA2dj6wFk4ajDpSBog2dj6x9nY+seQ**eb8EAA**AAMAAA**OsKsWxMqzKnukqE43scHW//ocS/gXuL/MApTzF/frcZ0Jt9yWqvLDujM+x1mAYti5KnBco7CFwYBd7WkrauwVAHDG/mZ59CRxYSH7fT3deo0mMhLXWiZ+JDgtBKcomZ+HQkPWc9ftYCaMMqsWUYT8fTwOsDbr++yo4bSGofD1NSZSTb9uUZfO3Sg5gK9L3xgrBHF/nR7coKzWlAwa0rgtMuh8HYKvtVO0IYpXAJZniLkgyBa4BZ+POcYOh1l1PHTdazz4Wadco2G3u/RP9ust14ksFMZ/Q/z5Rra86a7ymEouScCD2AS3guY/YwqsUo92seueNEDGCbSASNz5TMb6cBbCleNHw1pe5C1wlWJDWW0zFIQAcVaY/cRWykhqAX+quDPM/p4gz8gDg+T2o9JoThD3ZNVVQJwz6JLCHXPYTNZBS9w2dAidAi3kvlMIVWIhPlNmviT6wYSPS463Xmh1/i0AApToYrCLpnfggZicGLbRBi90PUz4ZqTYM8WXvhtPSKhatTVszGRAI7xISQbbIS4LkRmnFgkbRyuaJmNzzr91orygpRq1z7WJN3ClKwYrDxHXCBI+M6LkWUQWxJ9Jyd9Qg8ScE6n/B3UVcAfsgB97Du6RKvOgmZraxoQnG31lNeDqi4j0q9np9mga/RuMvEf13HURWU0dejPGsINCjF1ljkHR2wR2SPkxJwqCxCr/K4B9M8ZRDUSS7Q/re2m6WhfdoExUhwZ0hu/uIZWW4NaOvVja0VSEGASMRz3dsLY'
    EBAY_API_GET_CATEGORIES_URL = 'https://api.sandbox.ebay.com/ws/api.dll'
    REQUEST_TIMEOUT = 10

    def __init__(self):
        # Initial object setup (log a trace point).
        logging.info(
            self.__class__.__name__,
            'initialization',
            str(datetime.now())
        )

    @classmethod
    def getCategories(self):
        data = None
        headers = {
            'X-EBAY-API-SITEID': 0,
            'X-EBAY-API-COMPATIBILITY-LEVEL': 861,
            'X-EBAY-API-CALL-NAME': 'GetCategories',
            'X-EBAY-API-APP-NAME': 'EchoBay62-5538-466c-b43b-662768d6841',
            'X-EBAY-API-CERT-NAME': '00dd08ab-2082-4e3c-9518-5f4298f296db',
            'X-EBAY-API-DEV-NAME': '16a26b1b-26cf-442d-906d-597b60c41c19'
        }

        # Maybe it makes sense to keep it out of code?!
        # It looks ugly now.
        # For example, store it in db, conf, template, etc.
        # But for the test task, let it be here.
        payload = '''<?xml version="1.0" encoding="utf-8"?>
         <GetCategoriesRequest xmlns="urn:ebay:apis:eBLBaseComponents">
         <CategorySiteID>0</CategorySiteID>
         <ViewAllNodes>True</ViewAllNodes>
         <DetailLevel>ReturnAll</DetailLevel>
         <RequesterCredentials>
         <eBayAuthToken>''' + self.EBAY_API_TOKEN + '''</eBayAuthToken>
         </RequesterCredentials>
         </GetCategoriesRequest>'''

        try:
            r = requests.post(
                self.EBAY_API_GET_CATEGORIES_URL,
                data = payload,
                headers = headers,
                timeout = self.REQUEST_TIMEOUT
            )
            r.raise_for_status()
            data = r.content
        except requests.exceptions.RequestException as e:
            print('Request error: ', e)
        except requests.exceptions.HTTPError as e:
            print('HTTP error: ', e)
        except requests.exceptions.ConnectionError as e:
            print('Connect error: ', e)
        except requests.exceptions.Timeout as e:
            print('Timeout error: ', e)

        return data
#===============================================================================



# Class for working with DataBase.
#===============================================================================
class DB:
    __filename  = 'storage'
    __tablename = 'category'

    def __init__(self):
        # Initial object setup (log a trace point).
        logging.info(
            self.__class__.__name__,
            'initialization',
            str(datetime.now())
        )

    @property
    def filename(self):
        return self.__filename

    @classmethod
    def exists(self):
        if not os.path.isfile(self.__filename):
            return False

        # SQLite database file header is 100 bytes
        if os.path.getsize(self.__filename) < 100:
            return False

        with open(self.__filename, 'rb') as fd:
            header = fd.read(100)

        return 'sqlite' in header[:16].decode('utf-8').lower()

    @classmethod
    def createCategoryTable(self):
        # SQL code of table.
        # Table is just one, so PRIMARY KEY is replaced with UNIQUE.
        sql = '''CREATE TABLE ''' + self.__tablename + '''(
                 id INTEGER UNIQUE,
                 name TEXT NOT NULL,
                 parent INTEGER NOT NULL,
                 level INTEGER NOT NULL,
                 is_best_offer_enabled INTEGER DEFAULT 0
              )'''

        try:
            connection = sqlite3.connect(self.__filename)
            cursor = connection.cursor()
            cursor.execute(sql)
            connection.commit()

        except Error as e:
            print('Something is wrong with the database: ', e)
            # Quit, cause rest of actions doesn't make sense without db.
            quit()

        finally:
            connection.close()

    @classmethod
    def drop(self):
        # Remove DB.
        if os.path.exists(self.__filename):
            os.remove(self.__filename)
        else:
            print('The file does not exist!')

    @classmethod
    def addDataToCategoryTable(self, data = None):
        try:
            connection = sqlite3.connect(self.__filename)
            cursor = connection.cursor()
            cursor.executemany('INSERT INTO ' + self.__tablename + ' VALUES (?, ?, ?, ?, ?)', data)
            connection.commit()

        except Error as e:
            print('Something is wrong with the database: ', e)
            # Quit, cause rest of actions doesn't make sense without db.
            quit()

        finally:
            connection.close()

    @classmethod
    def getRootCategory(self, catid):
        # SQL code of query.
        sql = 'SELECT * FROM ' + self.__tablename + ' WHERE id = ' + catid

        try:
            connection = sqlite3.connect(self.__filename)
            cursor = connection.cursor()
            category = cursor.execute(sql).fetchone()
            connection.commit()

            return category

        except Error as e:
            print('Something is wrong with the database: ', e)
            # Quit, cause rest of actions doesn't make sense without db.
            quit()

        finally:
            connection.close()

    @classmethod
    def getSubCategories(self, parent):
        # SQL code of query.
        sql = 'SELECT * FROM ' + self.__tablename + ' WHERE parent = ' + str(parent)

        try:
            connection = sqlite3.connect(self.__filename)
            cursor = connection.cursor()
            categories = cursor.execute(sql).fetchall()
            connection.commit()

            return categories

        except Error as e:
            print('Something is wrong with the database: ', e)
            # Quit, cause rest of actions doesn't make sense without db.
            quit()

        finally:
            connection.close()
#===============================================================================



# Core App class
# To prevent multiple requests to api and db collisions, i use Singleton
# It doesn't make sense in challenge task, but good practice for large project.
#===============================================================================
class App:
    __instance = None
    __db = DB()

    @staticmethod
    def getInstance():
        if App.__instance is None:
            App()
        return App.__instance

    def __init__(self):
        if App.__instance is not None:
            raise Exception('Only one instance is allowed at the same time!')
        else:
            App.__instance = self

    @classmethod
    def rebuild(self):
        # Remove DB if it exists.
        if self.__db.exists(): self.__db.drop()

        # Create `category` table.
        self.__db.createCategoryTable()

        # Make ebay api request.
        data = EBayRequest().getCategories()

        if data is not None:
            # Parse XML
            formattedData = self._parseXMLtoArray(data)

            # Add data to `categories` table.
            self.__db.addDataToCategoryTable(formattedData)

        else:
            raise Exception('No data found in answer!')

    @classmethod
    def render(self, catid):
        # If my version of SQLite were higher than 3.8.3 I would use
        # Hierarchical and recursive queries in SQL for data obtaining
        # (this would help me avoid multiple requests to DB)
        # https://en.wikipedia.org/wiki/Hierarchical_and_recursive_queries_in_SQL
        #
        # SQL code example:
        # WITH RECURSIVE
        #    t(id, parent, name) AS (
        #      SELECT id, parent, name
        #          FROM t2
        #          WHERE parent=%catid%
        #      UNION ALL
        #          SELECT t3.id, t3.parent, t3.name
        #              FROM t2 AS t3
        #              INNER JOIN t AS t4 ON (t3.parent=t4.id)
        #    )
        #  SELECT id, parent, name FROM t;
        #
        # but my version is 3.8.2 ;(((, so i will use simple recursive queries.

        # Get root category data.
        root = self.__db.getRootCategory(catid)

        if root is None:
            raise Exception('No category with ID: ' + catid + '!')

        pluginJson = {
            "core": {
                "data": [
                    {
                        "text": list(root)[1],
                        "children": self._recursiveMapping([], catid)
                    }
                ]
            }
        }

        # Get page content.
        content = self._generatePage(json.dumps(pluginJson), catid)

        # Save html content to file.
        with open(catid + '.html', 'w') as file:
            file.write(content)

    @classmethod
    def _generatePage(self, json, catid):
        html = '''
        <!DOCTYPE html>
        <html lang="en">
            <head>
                <meta charset="UTF-8" />
                <meta name="viewport" content="width=device-width, initial-scale=1" />
                <title>Render page with ID = ''' + catid + '''</title>
            </head>
            <body>
                <h1>RENDER CATEGORY ID: ''' + catid + '''</h1>
                <div id="container"></div>
                <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.1.1/jquery.min.js"></script>
                <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/jstree/3.3.5/themes/default/style.min.css" />
                <script src="https://cdnjs.cloudflare.com/ajax/libs/jstree/3.3.5/jstree.min.js"></script>
                <script>
                  $(function() {
                    $('#container').jstree(''' + json + ''');
                  });
                </script>
            </body>
        </html>
        '''

        return html

    @classmethod
    def _recursiveMapping(self, receiver, catid):
        for item in self.__db.getSubCategories(catid):
            children = self._recursiveMapping([], item[0])
            if len(children):
                receiver.append({
                    "text": item[1],
                    "children": children
                })
            else:
                receiver.append({"text": item[1]})

        return receiver

    @classmethod
    def _parseXMLtoArray(self, data):
        # Return array
        result = []

        # Form an xml tree from data.
        tree = ElementTree.fromstring(data)

        # Find all category sections.
        categories = tree.find('{urn:ebay:apis:eBLBaseComponents}CategoryArray')

        # Iterate and store data in result array.
        for category in categories:
            id     = int(category.find('{urn:ebay:apis:eBLBaseComponents}CategoryID').text)
            parent = int(category.find('{urn:ebay:apis:eBLBaseComponents}CategoryParentID').text)
            level  = int(category.find('{urn:ebay:apis:eBLBaseComponents}CategoryLevel').text)
            name   = category.find('{urn:ebay:apis:eBLBaseComponents}CategoryName').text

            # Exclude top category from list sub-categories.
            if parent == id:
                parent = 0 # or -1 or something else

            # If this field is returned as true, the corresponding category
            # supports Best Offers. If this field is not present, the category
            # does not support Best Offers. This field is not returned when
            # false.
            try:
                category.find('{urn:ebay:apis:eBLBaseComponents}BestOfferEnabled').text
                is_best_offer_enabled = 1
            except Exception:
                is_best_offer_enabled = 0

            item = [id, name, parent, level, is_best_offer_enabled]
            result.append(item)

        return result
#===============================================================================



# Application entry point.
#===============================================================================
try:
    # Start app
    app = App()

    # Check input params
    if '--rebuild' in sys.argv:
        # Rebuild mode
        if '--render' in sys.argv:
            raise Exception('Mutual mode is not allowed!')

        # Well, it's okey. Run rebuild.
        app.rebuild()

    elif '--render' in sys.argv:
        # Render mode (category id is expected)
        if '--rebuild' in sys.argv:
            raise Exception('Mutual mode is not allowed!')

        # Python has some strange list filter syntax, at least for me )).
        catid = [p for p in sys.argv if p.isdigit()]

        if not catid:
            raise Exception('No category id is specified!')

        if len(catid) > 1:
            raise Exception('Which one? Multiple categories ids!')

        # Oh, it's okey. Run render.
        app.render(catid[0])
    else:
        raise Exception('Incorrect use of the application. Invalid parameters.')

except Exception as e:
    print('Error: ', e)
#===============================================================================