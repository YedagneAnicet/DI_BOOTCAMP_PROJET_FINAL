import { ComponentFixture, TestBed } from '@angular/core/testing';

import { ListArticleBlogComponent } from './list-article-blog.component';

describe('ListArticleBlogComponent', () => {
  let component: ListArticleBlogComponent;
  let fixture: ComponentFixture<ListArticleBlogComponent>;

  beforeEach(async () => {
    await TestBed.configureTestingModule({
      declarations: [ ListArticleBlogComponent ]
    })
    .compileComponents();

    fixture = TestBed.createComponent(ListArticleBlogComponent);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
