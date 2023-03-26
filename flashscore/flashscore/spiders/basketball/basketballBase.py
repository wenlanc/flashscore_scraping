from flashscore.spiders.baseSpider import BaseSpider
import os

class BasketballBaseSpider(BaseSpider):
    sport_name = "basketball"
    sport_link = "basketball"

    @classmethod
    def sportName(cls):
        return cls.sport_name

    @classmethod
    def baseUrl(cls):
        return "/".join([super().baseUrl(), cls.sport_link])

    @classmethod
    def resultPath(cls):
        path = "/".join([super().resultPath(), cls.sport_name]) + ".xlsx"
        return path

    @classmethod
    def getSportId(cls):
        return super().getSportId(cls.sport_link)