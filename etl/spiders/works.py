# -*- coding: utf-8 -*-
import scrapy


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

