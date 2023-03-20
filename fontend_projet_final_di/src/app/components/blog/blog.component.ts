import { Component, OnInit } from '@angular/core';
import { ArticleBlogService } from 'src/app/services/article-blog.service';

@Component({
  selector: 'app-blog',
  templateUrl: './blog.component.html',
  styleUrls: ['./blog.component.scss'],
})
export class BlogComponent implements OnInit {
  ListeArticle: any;

  constructor(private _article: ArticleBlogService) {}

  ngOnInit() {
    this._article.getAllArticles().subscribe((response) => {
      this.ListeArticle = response;
    });
  }
}
