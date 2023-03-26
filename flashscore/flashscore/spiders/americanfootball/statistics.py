from flashscore.spiders.americanfootball.americanfootballBase import AmericanFootballBaseSpider
from flashscore.spiders.baseSpider import BaseSpider
import scrapy
from utils import SHEET_NAMES, saveToExcel, makeStatisticsKey, gloryTrim

class StatisticsSpider(AmericanFootballBaseSpider):
    task_name = "statistics"
    name = ".".join([AmericanFootballBaseSpider.sportName(), task_name])

    @classmethod
    def taskName(cls):
        return cls.task_name

    def __init__(self, *args, **kwargs):
        self.matches = kwargs.get("task")
        self.base_url = BaseSpider.baseUrl() + '/match/'
        self.summary = "/#match-summary/match-summary"
        self.statistics = "/#match-summary/match-statistics"

    def start_requests(self):
        for matchId in self.matches:
            yield scrapy.Request(url=self.base_url + matchId + self.summary, callback=self.parseSummary, cb_kwargs=dict(type="summary", matchId=matchId))

    def parseSummary(self, response, type, matchId):
        result = {}
        result["MATCH_ID"] = matchId
        descriptionSel = response.css("div[class*=description___]")

        if len(descriptionSel) == 1:
            countryStr = gloryTrim(descriptionSel.css("span[class*=country___]::text").get())
            result["COUNTRY"] = countryStr.strip()[:-1]
            description = gloryTrim(descriptionSel.css("span[class*=country___]>a::text").get())
            leagueRound = description.split("-")
            result["LEAGUE"] = leagueRound[0].strip()
            if len(leagueRound) > 1:
                result["LEAGUE_ROUND"] = "-".join(leagueRound[1:]).strip()

        if len(descriptionSel) > 1:
            descriptionSel0 = descriptionSel[0]
            countryStr = gloryTrim(descriptionSel0.css("span[class*=country___]::text").get())
            result["COUNTRY"] = countryStr.strip()[:-1]
            description = gloryTrim(descriptionSel0.css("span[class*=country___]>a::text").get())
            leagueRound = description.split("-")
            result["LEAGUE"] = leagueRound[0].strip()
            if len(leagueRound) > 1:
                result["LEAGUE_ROUND"] = "-".join(leagueRound[1:]).strip()


        primarySels = response.css("div[class*=wrapper___]")
        if len(primarySels) > 1:
            primarySel = primarySels[1];

            dateTime = gloryTrim(primarySel.css("div[class*=startTime___]>div::text").get())
            if( len(dateTime.split(" ")) > 1):
                result["MATCH_DATE"] = dateTime.split(" ")[0]
                result["MATCH_TIME"] = dateTime.split(" ")[1]
            else:
                result["MATCH_DATE"] = dateTime.split(" ")[0]

            result["HOME_TEAM_NAME"] = gloryTrim(primarySel.css("div[class*=home___] div[class*=participantName___]>a::text").get())
            result["AWAY_TEAM_NAME"] = gloryTrim(primarySel.css("div[class*=away___] div[class*=participantName___]>a::text").get())
            scoreTexts = primarySel.css("div[class*=matchInfo___]>div[class*=wrapper___]>span::text").getall()
            hG = aG = ""
            if len(scoreTexts)>0:
                hG = gloryTrim(scoreTexts[0])
            if len(scoreTexts)>1:
                aG = gloryTrim(scoreTexts[2])
            result["HOME_TEAM_FULL_TIME_SCORE"] = hG
            result["AWAY_TEAM_FULL_TIME_SCORE"] = aG

            result["MATCH_STATUS"] = gloryTrim(primarySel.css("div[class*=matchInfo___]>div[class*=status___]>span::text").get())

        detailSel = response.css("div[class*=verticalSections___]")
        staLimits = 0
        if len(detailSel) == 1:
            scoreCells = detailSel.css("div[class*=incidentsHeader___]")

            for scoreCell in scoreCells:
                staLimits += 1
                cells = scoreCell.css("div")
                quarter = gloryTrim(cells[0].css("::text").get())
                scores = cells[-1].css("span::text").getall()
                hG = aG = ""
                if len(scores)>0:
                    hG = gloryTrim(scores[0])
                if len(scores)>1:
                    aG = gloryTrim(scores[1])

                result[makeStatisticsKey(super().sportName(), staLimits, "HOME_TEAM", "score")] = hG
                result[makeStatisticsKey(super().sportName(), staLimits, "AWAY_TEAM", "score")] = aG


            infoTexts = detailSel.css("div[class*=data___]>span::text").getall()
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
        yield scrapy.Request(url=self.base_url + matchId + self.statistics + "/0", callback=self.parseStatistics, cb_kwargs=dict(type="statistics", matchId=matchId, result=result))

    def parseStatistics(self, response, type, matchId, result, idx = 0):

        statSel = response.css("div[id=detail]")
        if len(statSel) == 1:
            rowSels = statSel.css("div[class*=statRow___]")
            if len(rowSels) > 0:
                if (idx > 0):
                    for rowSel in rowSels:
                        stateCategory = rowSel.css("div[class*=statCategory___]")
                        homeValue = gloryTrim(stateCategory.css("div[class*=homeValue___]::text").get())
                        key = gloryTrim(stateCategory.css("div[class*=categoryName___]::text").get())
                        awayValue = gloryTrim(stateCategory.css("div[class*=awayValue___]::text").get())

                        hKey = makeStatisticsKey(super().sportName(), idx, "HOME", key)
                        aKey = makeStatisticsKey(super().sportName(), idx, "AWAY", key)

                        result[hKey] = homeValue
                        result[aKey] = awayValue
                idx += 1
                yield scrapy.Request(url=self.base_url + matchId + self.statistics + "/" + str(idx), callback=self.parseStatistics, cb_kwargs=dict(type="statistics", matchId=matchId, result=result, idx = idx))
            else:
                print(result)
                saveToExcel(result, super().sportName(), self.taskName())
        else:
            print(result)
            saveToExcel(result, super().sportName(), self.taskName())
