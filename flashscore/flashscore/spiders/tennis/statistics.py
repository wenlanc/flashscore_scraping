from flashscore.spiders.tennis.tennisBase import TennisBaseSpider
from flashscore.spiders.baseSpider import BaseSpider
import scrapy
from utils import SHEET_NAMES, saveToExcel, makeStatisticsKey, gloryTrim
import re
class StatisticsSpider(TennisBaseSpider):
    task_name = "statistics"
    name = ".".join([TennisBaseSpider.sportName(), task_name])

    @classmethod
    def taskName(cls):
        return cls.task_name

    def __init__(self, *args, **kwargs):
        self.matches = kwargs.get("task")
        self.base_url = BaseSpider.baseUrl() + '/match/'
        self.summary = "/#match-summary"
        self.statistics = "/match-statistics"

    def start_requests(self):
        for matchId in self.matches:
            yield scrapy.Request(url=self.base_url + matchId + self.summary, callback=self.parseSummary, cb_kwargs=dict(type="summary", matchId=matchId))

    def parseSummary(self, response, type, matchId):
        result = {}
        result["MATCH_ID"] = matchId
        descriptionSel = response.css("div.description")

        descriptionSel = response.css("div[class*=description___]")
        if len(descriptionSel) == 1:
            countryStr = gloryTrim(descriptionSel.css("span[class*=country___]::text").get())
            result["TOUR"] = countryStr.strip()[:-1]
            description = gloryTrim(descriptionSel.css("span[class*=country___]>a::text").get())

            city_Round = description.split(",")
            cityCountry = ""
            surfaceRound = ""
            if len(city_Round) < 2:
                cityCountry = description
            elif len(city_Round) < 3:
                cityCountry,surfaceRound = description.split(",")
            elif len(city_Round) < 4:
                cityCountry = description.split(",")[0] +"-"+ description.split(",")[1]
                surfaceRound = description.split(",")[2]

            cityCountrySplit = cityCountry.split("-")
            if len(cityCountrySplit) > 1:
                result["CITY"] = "-".join([cityCountrySplit[0].split("(")[0].strip()] + cityCountrySplit[1:])
            else:
                result["CITY"] = cityCountrySplit[0].split("(")[0].strip()

            result["COUNTRY"] = cityCountrySplit[0].split("(")[-1].split(")")[0].strip()
            result["SURFACE"] = surfaceRound.split("-")[0].strip()
            result["ROUND"] = "-".join(surfaceRound.split("-")[1:]).strip()

        elif(len(descriptionSel) > 1):
            descriptionSel1 = descriptionSel[0]
            countryStr = gloryTrim(descriptionSel1.css("span[class*=country___]::text").get())
            result["TOUR"] = countryStr.strip()[:-1]
            description = gloryTrim(descriptionSel1.css("span[class*=country___]>a::text").get())

            city_Round = description.split(",")
            cityCountry = ""
            surfaceRound = ""
            if len(city_Round) < 2:
                cityCountry = description
            elif len(city_Round) < 3:
                cityCountry,surfaceRound = description.split(",")
            elif len(city_Round) < 4:
                cityCountry = description.split(",")[0] +"-"+ description.split(",")[1]
                surfaceRound = description.split(",")[2]

            cityCountrySplit = cityCountry.split("-")
            if len(cityCountrySplit) > 1:
                result["CITY"] = "-".join([cityCountrySplit[0].split("(")[0].strip()] + cityCountrySplit[1:])
            else:
                result["CITY"] = cityCountrySplit[0].split("(")[0].strip()
            result["COUNTRY"] = cityCountrySplit[0].split("(")[-1].split(")")[0].strip()
            result["SURFACE"] = surfaceRound.split("-")[0].strip()
            result["ROUND"] = "-".join(surfaceRound.split("-")[1:]).strip()

        primarySels = response.css("div[class*=wrapper___]")
        if len(primarySels) > 0:
            primarySel = primarySels[0]
            if len(primarySels) > 1:
                primarySel = primarySels[1]

            dateTime = gloryTrim(primarySel.css("div[class*=startTime___]>div::text").get())
            if( len(dateTime.split(" ")) > 1):
                result["MATCH_DATE"] = dateTime.split(" ")[0]
                result["MATCH_TIME"] = dateTime.split(" ")[1]
            else:
                result["MATCH_DATE"] = dateTime.split(" ")[0]

            result["PLAYER1_NAME"] = gloryTrim("/".join(primarySel.css("div[class*=home___] a[class*=participantName___]::text").getall()))
            result["PLAYER2_NAME"] = gloryTrim("/".join(primarySel.css("div[class*=away___] a[class*=participantName___]::text").getall()))
            try:
                result["PLAYER1_RANK"] = re.findall('\d+',gloryTrim(primarySel.css("div[class*=home___] div[class*=participantRank___]::text").get()) )[0]
            except:
                pass
            try:
                result["PLAYER2_RANK"] = re.findall('\d+',gloryTrim(primarySel.css("div[class*=away___] div[class*=participantRank___]::text").get()) )[0]
            except:
                pass

            scoreTexts = primarySel.css("div[class*=matchInfo___]>div[class*=wrapper___]>span::text").getall()
            hG = aG = ""
            if len(scoreTexts)>0:
                hG = gloryTrim(scoreTexts[0])
            if len(scoreTexts)>1:
                aG = gloryTrim(scoreTexts[2])

            result["PLAYER1_FULL_TIME_SCORE"] = hG
            result["PLAYER2_FULL_TIME_SCORE"] = aG

            result["MATCH_STATUS"] = gloryTrim(primarySel.css("div[class*=matchInfo___]>div[class*=status___]>span::text").get())


        infoTexts = response.css("div[class*=data___]>span::text").getall()
        if(len(infoTexts) > 0):
            for infoText in infoTexts:
                if infoText.startswith("Attendance:"):
                    val = gloryTrim(infoText[12:])
                    if val[-1] == ",":
                        val = val[:-1]
                    result["ATTENDANCE"] = val
                elif infoText.startswith("Referee:"):
                    val = gloryTrim(infoText[9:])
                    if val[-1] == ",":
                        val = val[:-1]
                    result["REFEREE"] = val
                elif infoText.startswith("Venue:"):
                    val = gloryTrim(infoText[7:])
                    if val[-1] == ",":
                        val = val[:-1]
                    result["VENUE"] = val

        detailSel = response.css("div[class*=template___]")

        scores = detailSel.css("div[class*=part--1]")
        if(len(scores) > 0):
            result["SET1_PLAYER1_SCORE"] = gloryTrim(scores[0].css("::text").get())
            result["SET1_PLAYER1_TIE-BREAK"] = gloryTrim(scores[0].css("sup::text").get())
            if(len(scores) > 2):
                result["SET1_PLAYER2_SCORE"] = gloryTrim(scores[2].css("::text").get())
                result["SET1_PLAYER2_TIE-BREAK"] = gloryTrim(scores[2].css("sup::text").get())
            else:
                result["SET1_PLAYER2_SCORE"] = gloryTrim(scores[1].css("::text").get())
                result["SET1_PLAYER2_TIE-BREAK"] = gloryTrim(scores[1].css("sup::text").get())

        scores = detailSel.css("div[class*=part--2]")
        if(len(scores) > 0):
            result["SET2_PLAYER1_SCORE"] = gloryTrim(scores[0].css("::text").get())
            result["SET2_PLAYER1_TIE-BREAK"] = gloryTrim(scores[0].css("sup::text").get())
            if(len(scores) > 2):
                result["SET2_PLAYER2_SCORE"] = gloryTrim(scores[2].css("::text").get())
                result["SET2_PLAYER2_TIE-BREAK"] = gloryTrim(scores[2].css("sup::text").get())
            else:
                result["SET2_PLAYER2_SCORE"] = gloryTrim(scores[1].css("::text").get())
                result["SET2_PLAYER2_TIE-BREAK"] = gloryTrim(scores[1].css("sup::text").get())

        scores = detailSel.css("div[class*=part--3]")
        if(len(scores) > 0):
            result["SET3_PLAYER1_SCORE"] = gloryTrim(scores[0].css("::text").get())
            result["SET3_PLAYER1_TIE-BREAK"] = gloryTrim(scores[0].css("sup::text").get())
            if(len(scores) > 2):
                result["SET3_PLAYER2_SCORE"] = gloryTrim(scores[2].css("::text").get())
                result["SET3_PLAYER2_TIE-BREAK"] = gloryTrim(scores[2].css("sup::text").get())
            else:
                result["SET3_PLAYER2_SCORE"] = gloryTrim(scores[1].css("::text").get())
                result["SET3_PLAYER2_TIE-BREAK"] = gloryTrim(scores[1].css("sup::text").get())

        times = detailSel.css("div[class*=time--overall]")
        if(len(times) > 0):
            result["FULL_TIME_DURATION"] = gloryTrim(times[0].css("::text").get())
        times = detailSel.css("div[class*=time--0]")
        if(len(times) > 0):
            result["SET1_DURATION"] = gloryTrim(times[0].css("::text").get())
        times = detailSel.css("div[class*=time--1]")
        if(len(times) > 0):
            result["SET2_DURATION"] = gloryTrim(times[0].css("::text").get())
        times = detailSel.css("div[class*=time--2]")
        if(len(times) > 0):
            result["SET3_DURATION"] = gloryTrim(times[0].css("::text").get())


        # detailSel = response.css("table#parts")
        # rowSels = detailSel.css("tbody tr")
        #
        # for rowSel in rowSels:
        #     colSels = rowSel.css("td")
        #     player = 0
        #     idx = 0
        #     for scoreSel in colSels:
        #         if scoreSel.attrib["class"] == "server-home":
        #             player = 1
        #         elif scoreSel.attrib["class"] == "server-away":
        #             player = 2
        #         elif "score part" in scoreSel.attrib["class"]:
        #             idx += 1
        #             result["SET{}_PLAYER{}_SCORE".format(idx, player)] = gloryTrim(scoreSel.css("span::text").get())
        #             result["SET{}_PLAYER{}_TIE-BREAK".format(idx, player)] = gloryTrim(scoreSel.css("sup::text").get())
        #
        # durations = detailSel.css("tfoot tr>td.score::text").getall()
        # idx = 0
        # for duration in durations:
        #     if idx == 0:
        #         result["FULL_TIME_DURATION"] = gloryTrim(duration)
        #     else:
        #         result["SET{}_DURATION".format(idx)] = gloryTrim(duration)
        #     idx += 1

        yield scrapy.Request(url=self.base_url + matchId + self.summary + self.statistics + "/0", callback=self.parseStatistics, cb_kwargs=dict(type="statistics", matchId=matchId, result=result, idx = 0))

    def parseStatistics(self, response, type, matchId, result, idx = 0):

        statSel = response.css("div[id=detail]")
        if len(statSel) == 1:
            rowSels = statSel.css("div[class*=statRow___]")
            if len(rowSels) > 0:
                if (idx > 0 ):
                    for rowSel in rowSels:
                        stateCategory = rowSel.css("div[class*=statCategory___]")
                        homeValue = gloryTrim(stateCategory.css("div[class*=homeValue___]::text").get())
                        key = gloryTrim(stateCategory.css("div[class*=categoryName___]::text").get())
                        awayValue = gloryTrim(stateCategory.css("div[class*=awayValue___]::text").get())

                        hKey = makeStatisticsKey(super().sportName(), idx, "PLAYER1", key)
                        aKey = makeStatisticsKey(super().sportName(), idx, "PLAYER2", key)

                        if '(' in homeValue:
                            homeValue = homeValue.split("(")[-1].split(")")[0].strip()
                        if '(' in awayValue:
                            awayValue = awayValue.split("(")[-1].split(")")[0].strip()

                        result[hKey] = homeValue
                        result[aKey] = awayValue
                idx += 1
                yield scrapy.Request(url=self.base_url + matchId + self.summary + self.statistics + "/" + str(idx), callback=self.parseStatistics, cb_kwargs=dict(type="statistics", matchId=matchId, result=result, idx = idx))
            else:
                print(result)
                saveToExcel(result, super().sportName(), self.taskName())
        else:
            print(result)
            saveToExcel(result, super().sportName(), self.taskName())
