# -*- coding: utf-8 -*-
import scrapy
from scrapy.crawler import CrawlerProcess


class JobItem(scrapy.Item):
    title = scrapy.Field()
    location = scrapy.Field()
    price = scrapy.Field()
    params = scrapy.Field()


class WorksSpider(scrapy.Spider):
    name = "works"
    allowed_domains = ['gratka.pl']
    start_urls = ['https://gratka.pl/praca']

    def parse(self, response):
        for oneJobOffer in response.css("a.teaser"):
            item = JobItem()
            item["title"] = oneJobOffer.css("h2.teaser__title::text").extract()
            item["location"] = oneJobOffer.css("h3.teaser__location::text").extract()
            item["price"] = oneJobOffer.css("p.teaser__price::text").extract()
            item["params"] = oneJobOffer.css(".teaser__params > li::text").extract()

            yield item

        # next_page_url = response.css(".pagination__nextPage::attr(href)").extract_first()
        # if next_page_url:
        #     yield scrapy.Request(url=next_page_url, callback=self.parse)


process = CrawlerProcess({
    'USER_AGENT': 'Mozilla/4.0 (compatible; MSIE 7.0; Windows NT 5.1)',
    'FEED_FORMAT': 'json',
    'FEED_URI': 'data.json'
})

process.crawl(WorksSpider)
process.start()  # the script will block here until the crawling is finished
print('smierdzace dupsko Kamila')
