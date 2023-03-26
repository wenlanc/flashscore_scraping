import os
import json
import fire

def run(dirPath):
    result_Path = "result"
    fileList = []
    for file in os.listdir(os.path.join(result_Path, dirPath)):
        filePath = os.path.join(result_Path, dirPath, file)
        with open(filePath, "r") as fp:
            jsonObj = json.load(fp)
            if len(jsonObj) == 0:
                fileList.append(filePath)

    for file in fileList:
        os.remove(file)
        print("Removing {}...", file)


if __name__ == '__main__':
    fire.Fire(run)