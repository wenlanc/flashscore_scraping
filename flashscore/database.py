import pymysql
from threading import Lock
lock = Lock()

class DBManager(object):
    host = "localhost"
    user = "flashscore"
    password = "flashscore"
    db = "flashscore"
    charset = "utf8mb4"
    connection = None
    initialized = False

    @classmethod
    def init(cls, host="localhost", user="flashscore", password="flashscore", db="flashscore", charset='utf8mb4'):
        # Connect to the database
        lock.acquire()
        try:
            cls.connection = pymysql.connect(host=host,
                                        user=user,
                                        password=password,
                                        db=db,
                                        charset=charset,
                                        cursorclass=pymysql.cursors.DictCursor)
            cls.initialized = True
        except Exception as e:
            print("Database Exception occured - init\n", e)
            cls.initialized = False
        finally:
            lock.release() #release lock
    
    @classmethod
    def is_initialized(cls):
        return cls.initialized

    @classmethod
    def update(cls, sql, *val):
        res = True
        lock.acquire()
        try:
            with cls.connection.cursor() as cursor:
                cursor.execute(sql, (val))
            cls.connection.commit()
        except Exception as e:
            print("Database Exception occured - update\n", sql, val, e)
            res = False
        finally:
            lock.release() #release lock
        return res

    @classmethod
    def query(cls, sql, *args, fetch="one"):
        lock.acquire()
        try:
            cls.connection.commit()
            with cls.connection.cursor() as cursor:
                cursor.execute(sql, (args))
                if fetch == "one":
                    result = cursor.fetchone()
                elif fetch == "all":
                    result = cursor.fetchall()
                else:
                    result = None
        except Exception as e:
            result = None
            print("Database Exception occured - query\n", sql, args, e)
        finally:
            lock.release() #release lock
        
        return result

