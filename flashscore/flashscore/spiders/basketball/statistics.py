from flashscore.spiders.basketball.basketballBase import BasketballBaseSpider
from flashscore.spiders.baseSpider import BaseSpider
import scrapy
from utils import SHEET_NAMES, saveToExcel, makeStatisticsKey, gloryTrim

class StatisticsSpider(BasketballBaseSpider):
    task_name = "statistics"
    name = ".".join([BasketballBaseSpider.sportName(), task_name])

    @classmethod
    def taskName(cls):
        return cls.task_name

    def __init__(self, *args, **kwargs):
        self.matches = kwargs.get("task")
        self.base_url = BaseSpider.baseUrl() + '/match/'
        self.taskType = kwargs.get("taskType")
        self.summary = "/#match-summary"
        self.statistics = "/#match-summary/match-statistics"

    def start_requests(self):
        for matchId in self.matches:
            yield scrapy.Request(url=self.base_url + matchId + self.summary, callback=self.parseSummary, cb_kwargs=dict(type="summary", matchId=matchId))

    def parseSummary(self, response, type, matchId):
        result = {}
        result["MATCH_ID"] = matchId
        descriptionSel = response.css("div[class*=tournamentHeaderDescription]>div[class*=sportNavWrapper]")
        if len(descriptionSel) == 1:
            countryStr = gloryTrim(descriptionSel.css("span[class*=country]::text").get())
            result["COUNTRY"] = countryStr.strip()[:-1]
            description = gloryTrim(descriptionSel.css("span[class*=country]>a::text").get())
            leagueRound = description.split("-")
            result["LEAGUE"] = leagueRound[0].strip()
            if len(leagueRound) > 1:
                result["ROUND"] = "-".join(leagueRound[1:]).strip()

        elif(len(descriptionSel) > 1):
            descriptionSel1 = descriptionSel[0]
            countryStr = gloryTrim(descriptionSel1.css("span[class*=country]::text").get())
            result["COUNTRY"] = countryStr.strip()[:-1]
            description = gloryTrim(descriptionSel1.css("span[class*=country]>a::text").get())
            leagueRound = description.split("-")
            result["LEAGUE"] = leagueRound[0].strip()
            if len(leagueRound) > 1:
                result["ROUND"] = "-".join(leagueRound[1:]).strip()


        primarySels = response.css("div[class*=duelParticipant]")
        if len(primarySels) > 0:
            primarySel = primarySels[0]
            if len(primarySels) > 1:
                primarySel = primarySels[1]

            dateTime = gloryTrim(primarySel.css("div[class*=startTime]>div::text").get())
            if( len(dateTime.split(" ")) > 1):
                result["MATCH_DATE"] = dateTime.split(" ")[0]
                result["MATCH_TIME"] = dateTime.split(" ")[1]
            else:
                result["MATCH_DATE"] = dateTime.split(" ")[0]

            result["HOME_TEAM_NAME"] = gloryTrim(primarySel.css("div[class*=home] div[class*=participantName]>a::text").get())
            result["AWAY_TEAM_NAME"] = gloryTrim(primarySel.css("div[class*=away] div[class*=participantName]>a::text").get())

            scoreTexts = primarySel.css("div[class*=matchInfo]>div[class*=wrapper]>span::text").getall()
            hG = aG = ""
            if len(scoreTexts)>0:
                hG = gloryTrim(scoreTexts[0])
            if len(scoreTexts)>1:
                aG = gloryTrim(scoreTexts[2])

            result["HOME_TEAM_FULL_TIME_SCORE"] = hG
            result["AWAY_TEAM_FULL_TIME_SCORE"] = aG

            result["MATCH_STATUS"] = gloryTrim(primarySel.css("div[class*=matchInfo]>div[class*=status]>span::text").get())


        infoTexts = response.css("div[class*=data]>span::text").getall()
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

        detailSel = response.css("div[class*=template]")
        rowSels = detailSel.css("div[class*=home]")[2:]
        try:
            rowSel = rowSels[0]
            score = rowSel.css("::text").get()
            score = gloryTrim(score.replace("<sup></sup>",""))
            result["HOME_TEAM_OVERTIME_SCORE"] = score
        except:
            pass

        try:
            rowSel = rowSels[1]
            score = rowSel.css("::text").get()
            score = gloryTrim(score.replace("<sup></sup>",""))
            result["HOME_TEAM_1ST_QUARTER_SCORE"] = score
        except:
            pass

        try:
            rowSel = rowSels[2]
            score = rowSel.css("::text").get()
            score = gloryTrim(score.replace("<sup></sup>",""))
            result["HOME_TEAM_2ND_QUARTER_SCORE"] = score
        except:
            pass

        try:
            rowSel = rowSels[3]
            score = rowSel.css("::text").get()
            score = gloryTrim(score.replace("<sup></sup>",""))
            result["HOME_TEAM_3RD_QUARTER_SCORE"] = score
        except:
            pass

        try:
            rowSel = rowSels[4]
            score = rowSel.css("::text").get()
            score = gloryTrim(score.replace("<sup></sup>",""))
            result["HOME_TEAM_4TH_QUARTER_SCORE"] = score
        except:
            pass

        rowSels = detailSel.css("div[class*=away]")[2:]
        try:
            rowSel = rowSels[0]
            score = rowSel.css("::text").get()
            score = gloryTrim(score.replace("<sup></sup>",""))
            result["AWAY_TEAM_OVERTIME_SCORE"] = score
        except:
            pass

        try:
            rowSel = rowSels[1]
            score = rowSel.css("::text").get()
            score = gloryTrim(score.replace("<sup></sup>",""))
            result["AWAY_TEAM_1ST_QUARTER_SCORE"] = score
        except:
            pass

        try:
            rowSel = rowSels[2]
            score = rowSel.css("::text").get()
            score = gloryTrim(score.replace("<sup></sup>",""))
            result["AWAY_TEAM_2ND_QUARTER_SCORE"] = score
        except:
            pass

        try:
            rowSel = rowSels[3]
            score = rowSel.css("::text").get()
            score = gloryTrim(score.replace("<sup></sup>",""))
            result["AWAY_TEAM_3RD_QUARTER_SCORE"] = score
        except:
            pass

        try:
            rowSel = rowSels[4]
            score = rowSel.css("::text").get()
            score = gloryTrim(score.replace("<sup></sup>",""))
            result["AWAY_TEAM_4TH_QUARTER_SCORE"] = score
        except:
            pass


        # Old solution
        # detailSel = response.css("table#parts")
        # if len(detailSel) == 1:
        #     result["HOME_TEAM_1ST_QUARTER_SCORE"] = gloryTrim(detailSel.css("span.p1_home::text").get())
        #     result["AWAY_TEAM_1ST_QUARTER_SCORE"] = gloryTrim(detailSel.css("span.p1_away::text").get())
        #
        #     result["HOME_TEAM_2ND_QUARTER_SCORE"] = gloryTrim(detailSel.css("span.p2_home::text").get())
        #     result["AWAY_TEAM_2ND_QUARTER_SCORE"] = gloryTrim(detailSel.css("span.p2_away::text").get())
        #
        #     result["HOME_TEAM_3RD_QUARTER_SCORE"] = gloryTrim(detailSel.css("span.p3_home::text").get())
        #     result["AWAY_TEAM_3RD_QUARTER_SCORE"] = gloryTrim(detailSel.css("span.p3_away::text").get())
        #
        #     result["HOME_TEAM_4TH_QUARTER_SCORE"] = gloryTrim(detailSel.css("span.p4_home::text").get())
        #     result["AWAY_TEAM_4TH_QUARTER_SCORE"] = gloryTrim(detailSel.css("span.p4_away::text").get())
        #
        #     result["HOME_TEAM_OVERTIME_SCORE"] = gloryTrim(detailSel.css("span.p5_home::text").get())
        #     result["AWAY_TEAM_OVERTIME_SCORE"] = gloryTrim(detailSel.css("span.p5_away::text").get())

        yield scrapy.Request(url=self.base_url + matchId + self.statistics, callback=self.parseStatistics, cb_kwargs=dict(type="statistics", matchId=matchId, result=result))

    def parseStatistics(self, response, type, matchId, result, idx = 0):

        statSel = response.css("div[id=detail]>div[class*=section]")
        if len(statSel) == 1:
            rowSels = statSel.css("div[class*=statRow___]")
            if len(rowSels) > 0:
                if (idx > 0):
                    for rowSel in rowSels:
                        stateCategory = rowSel.css("div[class*=statCategory]")
                        homeValue = gloryTrim(stateCategory.css("div[class*=statHomeValue]::text").get())
                        key = gloryTrim(stateCategory.css("div[class*=statCategoryName]::text").get())
                        awayValue = gloryTrim(stateCategory.css("div[class*=statAwayValue]::text").get())

                        hKey = makeStatisticsKey(super().sportName(), idx, "HOME", key)
                        aKey = makeStatisticsKey(super().sportName(), idx, "AWAY", key)
                        
                        if '(' in homeValue:
                            homeValue = homeValue.split("(")[-1].split(")")[0].strip()
                        if '(' in awayValue:
                            awayValue = awayValue.split("(")[-1].split(")")[0].strip()

                        result[hKey] = homeValue
                        result[aKey] = awayValue

                idx += 1
                yield scrapy.Request(url=self.base_url + matchId + self.statistics + "/" + str(idx), callback=self.parseStatistics, cb_kwargs=dict(type="statistics", matchId=matchId, result=result, idx = idx))
            else:
                print(result)
                saveToExcel(result, super().sportName(), self.taskName())
        else:
            print(result)
            saveToExcel(result, super().sportName(), self.taskName(), self.taskType)
