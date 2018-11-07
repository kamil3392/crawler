# -*- coding: utf-8 -*-

# Define your item pipelines here
#
# Don't forget to add your pipeline to the ITEM_PIPELINES setting
# See: https://doc.scrapy.org/en/latest/topics/item-pipeline.html

import sys
import MySQLdb
import hashlib
from scrapy.exceptions import DropItem
from scrapy.http import Request

class EtlPipeline(object):

    def __init__(self):
        self.conn = MySQLdb.connect('host', 'user', 'passwd','dbname', charset="utf8",use_unicode=True)
        self.cursor = self.conn.cursor()

    def process_item(self, item, spider):
        return item