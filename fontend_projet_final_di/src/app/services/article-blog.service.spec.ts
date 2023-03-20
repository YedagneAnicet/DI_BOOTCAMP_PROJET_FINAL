import { TestBed } from '@angular/core/testing';

import { ArticleBlogService } from './article-blog.service';

describe('ArticleBlogService', () => {
  let service: ArticleBlogService;

  beforeEach(() => {
    TestBed.configureTestingModule({});
    service = TestBed.inject(ArticleBlogService);
  });

  it('should be created', () => {
    expect(service).toBeTruthy();
  });
});
