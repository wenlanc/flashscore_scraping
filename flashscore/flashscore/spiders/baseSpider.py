import scrapy
import os
from database import DBManager

class BaseSpider(scrapy.Spider):
    allowed_domains = ['flashscore.com']
    base_url = 'https://www.flashscore.com'
    league_url = 'https://www.flashscore.com/x/req'

    result_path = "result"

    @classmethod
    def baseUrl(cls):
        return cls.base_url

    @classmethod
    def resultPath(cls):
        path = cls.result_path
        if not os.path.exists(path):
            os.makedirs(path)
        return path

    @classmethod
    def leagueUrl(cls):
        return cls.league_url

    @classmethod
    def getSportId(cls, name):
        sql = "SELECT id FROM sport WHERE link=%s"
        res = DBManager.query(sql, name)
        if res == None:
            return -1
        else:
            return res['id']
